<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Branch;
use App\Models\Guest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GuestController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $activeBranch = $request->attributes->get('active_branch') ?? Branch::first();

        $query = Guest::where('organization_id', $activeBranch->organization_id)
            ->withCount('reservations')
            ->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('id_number', 'like', "%{$search}%");
            });
        }

        $guests = $query->paginate(15)->withQueryString();

        return view('guests.index', compact('guests', 'search'));
    }

    public function show(Guest $guest): View
    {
        $guest->load(['reservations.branch', 'reservations.reservationRooms.room.roomType']);
        return view('guests.show', compact('guest'));
    }

    public function store(Request $request): RedirectResponse
    {
        $activeBranch = $request->attributes->get('active_branch') ?? Branch::first();

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:100'],
            'phone' => ['required', 'string', 'max:30'],
            'id_type' => ['required', 'in:nid,passport,driving_license,other'],
            'id_number' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'vip_status' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $guest = Guest::create(array_merge($validated, [
            'organization_id' => $activeBranch->organization_id,
            'vip_status' => $request->boolean('vip_status'),
        ]));

        AuditLog::log('guest.registered', $guest);

        return back()->with('success', "Guest {$guest->full_name} registered successfully.");
    }
}
