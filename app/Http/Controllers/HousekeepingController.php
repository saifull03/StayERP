<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Branch;
use App\Models\HousekeepingTask;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HousekeepingController extends Controller
{
    public function index(Request $request): View
    {
        $activeBranchId = $request->attributes->get('active_branch_id');
        $status = $request->input('status');

        $query = HousekeepingTask::with(['room.roomType', 'assignedTo', 'inspectedBy', 'branch'])
            ->latest('updated_at');

        if ($activeBranchId) {
            $query->where('branch_id', $activeBranchId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $tasks = $query->paginate(15)->withQueryString();

        $rooms = Room::when($activeBranchId, fn($q) => $q->where('branch_id', $activeBranchId))
            ->where('is_active', true)
            ->get();

        $staff = User::whereHas('branchAssignments', function ($q) use ($activeBranchId) {
            $q->whereHas('role', fn($r) => $r->whereIn('slug', ['housekeeping', 'branch-manager']));
            if ($activeBranchId) {
                $q->where('branch_id', $activeBranchId);
            }
        })->get();

        return view('housekeeping.index', compact('tasks', 'rooms', 'staff', 'status'));
    }

    public function store(Request $request): RedirectResponse
    {
        $activeBranchId = $request->attributes->get('active_branch_id') ?? Branch::first()->id;

        $validated = $request->validate([
            'room_id' => ['required', 'exists:rooms,id'],
            'assigned_to_user_id' => ['nullable', 'exists:users,id'],
            'priority' => ['required', 'in:low,medium,high,urgent'],
            'cleaning_type' => ['required', 'in:stayover,checkout,deep_clean,touchup'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $task = HousekeepingTask::create(array_merge($validated, [
            'branch_id' => $activeBranchId,
            'status' => 'dirty',
            'scheduled_for' => now()->toDateString(),
        ]));

        // Set room status to cleaning/dirty
        $task->room->update(['operational_status' => 'cleaning']);

        AuditLog::log('housekeeping.task_created', $task);

        return back()->with('success', "Housekeeping task logged for Room {$task->room->room_number}.");
    }

    public function updateStatus(Request $request, HousekeepingTask $task): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:dirty,cleaning,awaiting_inspection,clean'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $updateData = ['status' => $validated['status']];
        if (isset($validated['notes'])) {
            $updateData['notes'] = $validated['notes'];
        }

        if ($validated['status'] === 'cleaning' && !$task->started_at) {
            $updateData['started_at'] = now();
        }

        if ($validated['status'] === 'clean') {
            $updateData['completed_at'] = now();
            $updateData['inspected_by_user_id'] = $request->user()->id;
            $updateData['inspected_at'] = now();

            // Set room back to available
            $task->room->update([
                'operational_status' => 'available',
                'operational_notes' => 'Inspected and certified clean by ' . $request->user()->name . ' on ' . now()->toFormattedDateString(),
            ]);
        } elseif ($validated['status'] === 'cleaning') {
            $task->room->update(['operational_status' => 'cleaning']);
        }

        $task->update($updateData);

        AuditLog::log('housekeeping.status_updated', $task, null, ['status' => $task->status]);

        return back()->with('success', "Room {$task->room->room_number} housekeeping status updated to " . ucfirst(str_replace('_', ' ', $task->status)) . ".");
    }
}
