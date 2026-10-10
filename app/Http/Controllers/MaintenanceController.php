<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Branch;
use App\Models\MaintenanceRequest;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MaintenanceController extends Controller
{
    public function index(Request $request): View
    {
        $activeBranchId = $request->attributes->get('active_branch_id');
        $status = $request->input('status');

        $query = MaintenanceRequest::with(['room.roomType', 'reportedBy', 'assignedTo', 'branch'])
            ->latest('updated_at');

        if ($activeBranchId) {
            $query->where('branch_id', $activeBranchId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $requests = $query->paginate(15)->withQueryString();

        $rooms = Room::when($activeBranchId, fn($q) => $q->where('branch_id', $activeBranchId))
            ->where('is_active', true)
            ->get();

        $staff = User::whereHas('branchAssignments', function ($q) use ($activeBranchId) {
            $q->whereHas('role', fn($r) => $r->whereIn('slug', ['maintenance', 'branch-manager']));
            if ($activeBranchId) {
                $q->where('branch_id', $activeBranchId);
            }
        })->get();

        return view('maintenance.index', compact('requests', 'rooms', 'staff', 'status'));
    }

    public function store(Request $request): RedirectResponse
    {
        $activeBranchId = $request->attributes->get('active_branch_id') ?? Branch::first()->id;

        $validated = $request->validate([
            'room_id' => ['nullable', 'exists:rooms,id'],
            'assigned_to_user_id' => ['nullable', 'exists:users,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:1000'],
            'priority' => ['required', 'in:low,medium,high,critical'],
            'blocks_room' => ['nullable', 'boolean'],
            'estimated_cost' => ['nullable', 'numeric', 'min:0'],
        ]);

        $blocksRoom = $request->boolean('blocks_room');

        $mReq = MaintenanceRequest::create(array_merge($validated, [
            'branch_id' => $activeBranchId,
            'reported_by_user_id' => $request->user()->id,
            'status' => 'open',
            'blocks_room' => $blocksRoom,
            'estimated_cost' => $validated['estimated_cost'] ?? 0.00,
        ]));

        if ($blocksRoom && $mReq->room) {
            $mReq->room->update([
                'operational_status' => 'maintenance',
                'maintenance_reason' => $mReq->title,
            ]);
        }

        AuditLog::log('maintenance.created', $mReq);

        return back()->with('success', "Maintenance request #{$mReq->id} created.");
    }

    public function updateStatus(Request $request, MaintenanceRequest $maintenance): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:open,assigned,in_progress,resolved,closed'],
            'resolution_notes' => ['nullable', 'string', 'max:1000'],
            'actual_cost' => ['nullable', 'numeric', 'min:0'],
        ]);

        $updateData = ['status' => $validated['status']];
        if (isset($validated['resolution_notes'])) {
            $updateData['resolution_notes'] = $validated['resolution_notes'];
        }
        if (isset($validated['actual_cost'])) {
            $updateData['actual_cost'] = $validated['actual_cost'];
        }

        if (in_array($validated['status'], ['resolved', 'closed'])) {
            $updateData['resolved_at'] = now();

            // Unblock room if it was blocked
            if ($maintenance->blocks_room && $maintenance->room) {
                $maintenance->room->update([
                    'operational_status' => 'available',
                    'maintenance_reason' => null,
                ]);
            }
        } elseif ($maintenance->blocks_room && $maintenance->room) {
            $maintenance->room->update([
                'operational_status' => 'maintenance',
                'maintenance_reason' => $maintenance->title,
            ]);
        }

        $maintenance->update($updateData);

        AuditLog::log('maintenance.status_updated', $maintenance, null, ['status' => $maintenance->status]);

        return back()->with('success', "Maintenance request #{$maintenance->id} status updated to " . ucfirst(str_replace('_', ' ', $maintenance->status)) . ".");
    }
}
