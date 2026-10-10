<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Branch;
use App\Models\Guest;
use App\Models\GuestFolioItem;
use App\Models\HousekeepingTask;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\ReservationRateSnapshot;
use App\Models\ReservationRoom;
use App\Models\Room;
use App\Models\RoomType;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReservationController extends Controller
{
    public function index(Request $request): View
    {
        $activeBranchId = $request->attributes->get('active_branch_id');
        $status = $request->input('status');
        $search = $request->input('search');

        $query = Reservation::with(['guest', 'reservationRooms.room', 'reservationRooms.roomType', 'branch'])
            ->latest('created_at');

        if ($activeBranchId) {
            $query->where('branch_id', $activeBranchId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('reservation_number', 'like', "%{$search}%")
                  ->orWhereHas('guest', function ($g) use ($search) {
                      $g->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $reservations = $query->paginate(15)->withQueryString();

        return view('reservations.index', compact('reservations', 'status', 'search'));
    }

    public function create(Request $request): View
    {
        $activeBranch = $request->attributes->get('active_branch') ?? Branch::first();
        $roomTypes = RoomType::where('branch_id', $activeBranch->id)->where('is_active', true)->get();
        $guests = Guest::where('organization_id', $activeBranch->organization_id)->get();
        $rooms = Room::where('branch_id', $activeBranch->id)->where('is_active', true)->with('roomType')->get();

        return view('reservations.create', compact('activeBranch', 'roomTypes', 'guests', 'rooms'));
    }

    public function store(Request $request): RedirectResponse
    {
        $activeBranchId = $request->attributes->get('active_branch_id') ?? $request->input('branch_id');
        $branch = Branch::findOrFail($activeBranchId);

        $validated = $request->validate([
            'guest_type' => ['required', 'in:existing,new'],
            'guest_id' => ['required_if:guest_type,existing', 'nullable', 'exists:guests,id'],
            'first_name' => ['required_if:guest_type,new', 'nullable', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'phone' => ['required_if:guest_type,new', 'nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:100'],
            'id_type' => ['nullable', 'in:nid,passport,driving_license,other'],
            'id_number' => ['nullable', 'string', 'max:100'],
            'room_id' => ['required', 'exists:rooms,id'],
            'check_in_date' => ['required', 'date'],
            'check_out_date' => ['required', 'date', 'after:check_in_date'],
            'adults' => ['required', 'integer', 'min:1'],
            'children' => ['nullable', 'integer', 'min:0'],
            'source' => ['required', 'in:walk_in,direct,online,phone'],
            'special_requests' => ['nullable', 'string', 'max:500'],
            'advance_payment' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['required_if:advance_payment,>0', 'nullable', 'in:cash,card,bank_transfer,bkash,nagad,rocket'],
        ]);

        $room = Room::with('roomType')->findOrFail($validated['room_id']);

        // Concurrency Lock & Double Booking Prevention
        return DB::transaction(function () use ($validated, $branch, $room, $request) {
            // Check availability with row lock
            $roomLocked = Room::where('id', $room->id)->lockForUpdate()->first();
            if (!$roomLocked->isAvailableForDates($validated['check_in_date'], $validated['check_out_date'])) {
                return back()->withInput()->with('error', "Room {$room->room_number} is already booked or unavailable for selected dates.");
            }

            // Resolve Guest
            if ($validated['guest_type'] === 'new') {
                $guest = Guest::create([
                    'organization_id' => $branch->organization_id,
                    'first_name' => $validated['first_name'],
                    'last_name' => $validated['last_name'] ?? null,
                    'phone' => $validated['phone'],
                    'email' => $validated['email'] ?? null,
                    'id_type' => $validated['id_type'] ?? 'nid',
                    'id_number' => $validated['id_number'] ?? null,
                ]);
            } else {
                $guest = Guest::findOrFail($validated['guest_id']);
            }

            $checkIn = Carbon::parse($validated['check_in_date']);
            $checkOut = Carbon::parse($validated['check_out_date']);
            $nights = max(1, $checkIn->diffInDays($checkOut));
            $nightlyRate = (float) $room->roomType->base_price_per_night;
            $roomTotal = round($nightlyRate * $nights, 2);

            // Create Reservation
            $reservation = Reservation::create([
                'branch_id' => $branch->id,
                'guest_id' => $guest->id,
                'reservation_number' => Reservation::generateReservationNumber($branch->id),
                'check_in_date' => $validated['check_in_date'],
                'check_out_date' => $validated['check_out_date'],
                'status' => 'confirmed',
                'source' => $validated['source'],
                'adults' => $validated['adults'],
                'children' => $validated['children'] ?? 0,
                'special_requests' => $validated['special_requests'] ?? null,
                'created_by_user_id' => $request->user()->id,
            ]);

            // Assign Room
            ReservationRoom::create([
                'reservation_id' => $reservation->id,
                'room_id' => $room->id,
                'room_type_id' => $room->room_type_id,
                'nightly_rate' => $nightlyRate,
                'nights_count' => $nights,
                'total_amount' => $roomTotal,
            ]);

            // Snapshots per night
            $period = CarbonPeriod::create($checkIn, $checkOut->copy()->subDay());
            foreach ($period as $date) {
                ReservationRateSnapshot::create([
                    'reservation_id' => $reservation->id,
                    'stay_date' => $date->toDateString(),
                    'nightly_rate' => $nightlyRate,
                    'tax_amount' => round($nightlyRate * ($branch->tax_rate_percent / 100), 2),
                    'discount_amount' => 0.00,
                ]);
            }

            // Create Folio Item for Room Charge
            $folioItem = GuestFolioItem::create([
                'reservation_id' => $reservation->id,
                'branch_id' => $branch->id,
                'item_type' => 'room_charge',
                'description' => "Room {$room->room_number} ({$nights} nights @ BDT {$nightlyRate})",
                'quantity' => 1,
                'unit_price' => $roomTotal,
                'total_amount' => $roomTotal,
                'added_by_user_id' => $request->user()->id,
            ]);

            // Generate Invoice
            $taxAmount = round($roomTotal * ($branch->tax_rate_percent / 100), 2);
            $grandTotal = round($roomTotal + $taxAmount, 2);

            $invoice = Invoice::create([
                'branch_id' => $branch->id,
                'reservation_id' => $reservation->id,
                'invoice_number' => Invoice::generateInvoiceNumber($branch->id),
                'subtotal' => $roomTotal,
                'tax_amount' => $taxAmount,
                'discount_amount' => 0.00,
                'grand_total' => $grandTotal,
                'paid_amount' => 0.00,
                'balance_due' => $grandTotal,
                'status' => 'unpaid',
                'issued_date' => now()->toDateString(),
                'due_date' => $validated['check_out_date'],
                'notes' => 'Reservation invoice generated automatically.',
                'created_by_user_id' => $request->user()->id,
            ]);

            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'folio_item_id' => $folioItem->id,
                'description' => $folioItem->description,
                'quantity' => 1,
                'unit_price' => $roomTotal,
                'total_amount' => $roomTotal,
            ]);

            // If advance payment provided
            $advance = (float) ($validated['advance_payment'] ?? 0);
            if ($advance > 0) {
                Payment::create([
                    'branch_id' => $branch->id,
                    'reservation_id' => $reservation->id,
                    'invoice_id' => $invoice->id,
                    'payment_number' => Payment::generatePaymentNumber($branch->id),
                    'amount' => $advance,
                    'payment_method' => $validated['payment_method'],
                    'transaction_reference' => 'ADV-' . strtoupper(substr(bin2hex(random_bytes(2)), 0, 4)),
                    'status' => 'completed',
                    'notes' => 'Advance deposit collected at booking.',
                    'received_by_user_id' => $request->user()->id,
                    'paid_at' => now(),
                ]);

                $invoice->recalculateTotals();
            }

            AuditLog::log('reservation.created', $reservation, null, [
                'reservation_number' => $reservation->reservation_number,
                'room_number' => $room->room_number,
                'guest' => $guest->full_name,
            ]);

            return redirect()->route('reservations.show', $reservation)
                ->with('success', "Reservation {$reservation->reservation_number} confirmed successfully for Room {$room->room_number}.");
        });
    }

    public function show(Reservation $reservation): View
    {
        $reservation->load([
            'guest',
            'branch',
            'reservationRooms.room.roomType',
            'folioItems',
            'invoices.items',
            'payments.receivedBy',
            'refunds',
            'createdBy',
        ]);

        return view('reservations.show', compact('reservation'));
    }

    public function checkIn(Reservation $reservation): RedirectResponse
    {
        if (!in_array($reservation->status, ['confirmed', 'pending'])) {
            return back()->with('error', "Cannot check in reservation in '{$reservation->status}' status.");
        }

        $resRoom = $reservation->reservationRooms()->first();
        if ($resRoom && $resRoom->room) {
            // Update room status
            $resRoom->room->update(['operational_status' => 'available']);
        }

        $reservation->update([
            'status' => 'checked_in',
            'checked_in_at' => now(),
        ]);

        AuditLog::log('reservation.checkin', $reservation);

        return back()->with('success', "Guest {$reservation->guest->full_name} successfully checked into Room {$resRoom?->room?->room_number}.");
    }

    public function checkOut(Reservation $reservation): RedirectResponse
    {
        if ($reservation->status !== 'checked_in') {
            return back()->with('error', "Only checked-in reservations can be checked out.");
        }

        return DB::transaction(function () use ($reservation) {
            $reservation->update([
                'status' => 'checked_out',
                'checked_out_at' => now(),
            ]);

            // Mark room as cleaning and spawn Housekeeping Task
            $resRoom = $reservation->reservationRooms()->first();
            if ($resRoom && $resRoom->room) {
                $room = $resRoom->room;
                $room->update(['operational_status' => 'cleaning']);

                HousekeepingTask::create([
                    'branch_id' => $reservation->branch_id,
                    'room_id' => $room->id,
                    'priority' => 'high',
                    'status' => 'dirty',
                    'cleaning_type' => 'checkout',
                    'scheduled_for' => now()->toDateString(),
                    'notes' => "Automatic departure turnover task for Room {$room->room_number} (Guest: {$reservation->guest->full_name}).",
                ]);
            }

            AuditLog::log('reservation.checkout', $reservation);

            return back()->with('success', "Guest checked out. Room {$resRoom?->room?->room_number} marked as dirty and assigned for housekeeping.");
        });
    }

    public function cancel(Request $request, Reservation $reservation): RedirectResponse
    {
        if (in_array($reservation->status, ['checked_out', 'cancelled'])) {
            return back()->with('error', "Reservation is already {$reservation->status}.");
        }

        $validated = $request->validate([
            'cancellation_reason' => ['required', 'string', 'max:500'],
        ]);

        $reservation->update([
            'status' => 'cancelled',
            'cancellation_reason' => $validated['cancellation_reason'],
            'cancelled_at' => now(),
        ]);

        AuditLog::log('reservation.cancelled', $reservation, null, ['reason' => $validated['cancellation_reason']]);

        return back()->with('success', "Reservation {$reservation->reservation_number} has been cancelled.");
    }

    public function addFolioItem(Request $request, Reservation $reservation): RedirectResponse
    {
        $validated = $request->validate([
            'item_type' => ['required', 'in:restaurant,service,laundry,minibar,damage,other'],
            'description' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'integer', 'min:1'],
            'unit_price' => ['required', 'numeric', 'min:0'],
        ]);

        $totalAmount = round($validated['quantity'] * $validated['unit_price'], 2);

        DB::transaction(function () use ($validated, $totalAmount, $reservation, $request) {
            $folioItem = GuestFolioItem::create([
                'reservation_id' => $reservation->id,
                'branch_id' => $reservation->branch_id,
                'item_type' => $validated['item_type'],
                'description' => $validated['description'],
                'quantity' => $validated['quantity'],
                'unit_price' => $validated['unit_price'],
                'total_amount' => $totalAmount,
                'added_by_user_id' => $request->user()->id,
            ]);

            // Add to latest invoice
            $invoice = $reservation->latestInvoice ?? Invoice::create([
                'branch_id' => $reservation->branch_id,
                'reservation_id' => $reservation->id,
                'invoice_number' => Invoice::generateInvoiceNumber($reservation->branch_id),
                'subtotal' => 0,
                'tax_amount' => 0,
                'discount_amount' => 0,
                'grand_total' => 0,
                'paid_amount' => 0,
                'balance_due' => 0,
                'status' => 'unpaid',
                'issued_date' => now()->toDateString(),
                'created_by_user_id' => $request->user()->id,
            ]);

            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'folio_item_id' => $folioItem->id,
                'description' => $folioItem->description,
                'quantity' => $folioItem->quantity,
                'unit_price' => $folioItem->unit_price,
                'total_amount' => $folioItem->total_amount,
            ]);

            $invoice->recalculateTotals();

            AuditLog::log('folio.item_added', $reservation, null, [
                'description' => $validated['description'],
                'amount' => $totalAmount,
            ]);
        });

        return back()->with('success', "Charge of BDT " . number_format($totalAmount, 2) . " posted to guest folio.");
    }
}
