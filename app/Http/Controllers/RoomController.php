<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Branch;
use App\Models\Room;
use App\Models\RoomAmenity;
use App\Models\RoomType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoomController extends Controller
{
    public function index(Request $request): View
    {
        $activeBranchId = $request->attributes->get('active_branch_id');
        $status = $request->input('status');
        $type = $request->input('type');

        $query = Room::with(['roomType.amenities', 'branch'])->orderBy('floor')->orderBy('room_number');

        if ($activeBranchId) {
            $query->where('branch_id', $activeBranchId);
        }

        if ($status) {
            $query->where('operational_status', $status);
        }

        if ($type) {
            $query->where('room_type_id', $type);
        }

        $rooms = $query->get();

        $roomTypes = RoomType::when($activeBranchId, fn($q) => $q->where('branch_id', $activeBranchId))
            ->where('is_active', true)
            ->get();

        return view('rooms.index', compact('rooms', 'roomTypes', 'status', 'type'));
    }

    public function updateStatus(Request $request, Room $room): RedirectResponse
    {
        $validated = $request->validate([
            'operational_status' => ['required', 'in:available,cleaning,maintenance,out_of_order'],
            'maintenance_reason' => ['nullable', 'string', 'max:255'],
            'operational_notes' => ['nullable', 'string', 'max:500'],
        ]);

        $oldStatus = $room->operational_status;
        $room->update($validated);

        AuditLog::log('room.status_updated', $room, ['status' => $oldStatus], ['status' => $room->operational_status]);

        return back()->with('success', "Room {$room->room_number} status updated to " . ucfirst(str_replace('_', ' ', $room->operational_status)) . ".");
    }

    public function calendar(Request $request): View
    {
        $activeBranchId = $request->attributes->get('active_branch_id');
        $startDate = $request->input('start_date', now()->toDateString());
        $start = \Carbon\Carbon::parse($startDate);
        $dates = [];
        for ($i = 0; $i < 14; $i++) {
            $dates[] = $start->copy()->addDays($i);
        }

        $rooms = Room::when($activeBranchId, fn($q) => $q->where('branch_id', $activeBranchId))
            ->where('is_active', true)
            ->with(['roomType', 'reservationRooms.reservation' => function ($q) use ($dates) {
                $q->whereIn('status', ['confirmed', 'checked_in'])
                  ->where('check_in_date', '<', end($dates)->copy()->addDay()->toDateString())
                  ->where('check_out_date', '>', $dates[0]->toDateString());
            }])
            ->orderBy('room_number')
            ->get();

        return view('rooms.calendar', compact('rooms', 'dates', 'startDate'));
    }
}
