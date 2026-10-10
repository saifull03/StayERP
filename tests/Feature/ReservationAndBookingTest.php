<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Guest;
use App\Models\HousekeepingTask;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationAndBookingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_reservation_creation_and_invoice_generation(): void
    {
        $reception = User::where('email', 'reception.dhaka@stayinghotel.com')->first();
        $dhakaBranch = Branch::where('code', 'DHK')->first();
        $availableRoom = Room::where('branch_id', $dhakaBranch->id)
            ->where('operational_status', 'available')
            ->first();

        $checkIn = Carbon::today()->addDays(5)->toDateString();
        $checkOut = Carbon::today()->addDays(8)->toDateString();

        $response = $this->actingAs($reception)->post('/reservations', [
            'branch_id' => $dhakaBranch->id,
            'guest_type' => 'new',
            'first_name' => 'Kazi',
            'last_name' => 'Nazrul',
            'phone' => '+880 1711 999888',
            'email' => 'nazrul@example.com',
            'room_id' => $availableRoom->id,
            'check_in_date' => $checkIn,
            'check_out_date' => $checkOut,
            'adults' => 2,
            'children' => 0,
            'source' => 'direct',
            'advance_payment' => 5000.00,
            'payment_method' => 'cash',
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('guests', [
            'phone' => '+880 1711 999888',
            'first_name' => 'Kazi',
        ]);

        $guest = Guest::where('phone', '+880 1711 999888')->first();
        $reservation = Reservation::where('guest_id', $guest->id)->first();
        $this->assertNotNull($reservation);
        $this->assertEquals('confirmed', $reservation->status);

        // Check invoice was generated
        $invoice = $reservation->latestInvoice;
        $this->assertNotNull($invoice);
        $this->assertGreaterThan(0, $invoice->grand_total);
        $this->assertEquals(5000.00, $invoice->paid_amount);
    }

    public function test_double_booking_prevention_for_overlapping_dates(): void
    {
        $reception = User::where('email', 'reception.dhaka@stayinghotel.com')->first();
        $dhakaBranch = Branch::where('code', 'DHK')->first();
        $room = Room::where('branch_id', $dhakaBranch->id)
            ->where('operational_status', 'available')
            ->first();

        $checkIn = Carbon::today()->addDays(10)->toDateString();
        $checkOut = Carbon::today()->addDays(14)->toDateString();

        // 1. Create first booking
        $this->actingAs($reception)->post('/reservations', [
            'branch_id' => $dhakaBranch->id,
            'guest_type' => 'new',
            'first_name' => 'Guest',
            'last_name' => 'One',
            'phone' => '+880 1711 100001',
            'room_id' => $room->id,
            'check_in_date' => $checkIn,
            'check_out_date' => $checkOut,
            'adults' => 1,
            'source' => 'walk_in',
        ]);

        // 2. Attempt overlapping second booking for the same physical room (e.g. day 12 to 16)
        $overlappingIn = Carbon::today()->addDays(12)->toDateString();
        $overlappingOut = Carbon::today()->addDays(16)->toDateString();

        $response2 = $this->actingAs($reception)->post('/reservations', [
            'branch_id' => $dhakaBranch->id,
            'guest_type' => 'new',
            'first_name' => 'Guest',
            'last_name' => 'Two',
            'phone' => '+880 1711 100002',
            'room_id' => $room->id,
            'check_in_date' => $overlappingIn,
            'check_out_date' => $overlappingOut,
            'adults' => 1,
            'source' => 'walk_in',
        ]);

        $response2->assertSessionHas('error');

        // Confirm second reservation was rejected and not persisted
        $this->assertDatabaseMissing('guests', [
            'phone' => '+880 1711 100002',
        ]);
    }

    public function test_checkin_and_checkout_lifecycle(): void
    {
        $reception = User::where('email', 'reception.dhaka@stayinghotel.com')->first();
        $reservation = Reservation::where('status', 'confirmed')->first();
        $room = $reservation->reservationRooms->first()->room;

        // 1. Check in
        $responseIn = $this->actingAs($reception)->post("/reservations/{$reservation->id}/check-in");
        $responseIn->assertSessionHas('success');
        $this->assertEquals('checked_in', $reservation->fresh()->status);
        $this->assertNotNull($reservation->fresh()->checked_in_at);

        // 2. Check out
        $responseOut = $this->actingAs($reception)->post("/reservations/{$reservation->id}/check-out");
        $responseOut->assertSessionHas('success');
        $this->assertEquals('checked_out', $reservation->fresh()->status);
        $this->assertNotNull($reservation->fresh()->checked_out_at);

        // Room should be marked as cleaning
        $this->assertEquals('cleaning', $room->fresh()->operational_status);

        // Housekeeping turnover task should exist
        $this->assertTrue(HousekeepingTask::where('room_id', $room->id)->where('status', 'dirty')->exists());
    }

    public function test_posting_folio_service_charge_updates_invoice(): void
    {
        $reception = User::where('email', 'reception.dhaka@stayinghotel.com')->first();
        $reservation = Reservation::where('status', 'checked_in')->first();
        $initialDue = $reservation->balance_due;

        $response = $this->actingAs($reception)->post("/reservations/{$reservation->id}/folio-item", [
            'item_type' => 'restaurant',
            'description' => 'Fine Dining Executive Dinner',
            'quantity' => 2,
            'unit_price' => 1250.00,
        ]);

        $response->assertSessionHas('success');

        $this->assertEquals($initialDue + 2500.00, $reservation->fresh()->balance_due);
    }

    public function test_instant_guest_search_by_phone_and_email(): void
    {
        $reception = User::where('email', 'reception.dhaka@stayinghotel.com')->first();
        $guest = Guest::first();

        // 1. Search by phone digits
        $phoneSub = substr($guest->phone, -4);
        $responsePhone = $this->actingAs($reception)->getJson("/guests/search?q={$phoneSub}");
        $responsePhone->assertStatus(200);
        $responsePhone->assertJsonFragment([
            'id' => $guest->id,
            'phone' => $guest->phone,
        ]);

        // 2. Search by email
        if ($guest->email) {
            $emailSub = substr($guest->email, 0, 5);
            $responseEmail = $this->actingAs($reception)->getJson("/guests/search?q={$emailSub}");
            $responseEmail->assertStatus(200);
            $responseEmail->assertJsonFragment([
                'id' => $guest->id,
                'email' => $guest->email,
            ]);
        }

        // 3. Search by ID / Passport number
        if ($guest->id_number) {
            $idSub = substr($guest->id_number, 0, 5);
            $responseId = $this->actingAs($reception)->getJson("/guests/search?q={$idSub}");
            $responseId->assertStatus(200);
            $responseId->assertJsonFragment([
                'id' => $guest->id,
                'id_number' => $guest->id_number,
            ]);
        }
    }
}
