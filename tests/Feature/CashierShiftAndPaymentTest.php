<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\CashierShift;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CashierShiftAndPaymentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_cashier_shift_lifecycle_and_reconciliation(): void
    {
        $cashier = User::where('email', 'cashier.dhaka@stayinghotel.com')->first();
        $dhakaBranch = Branch::where('code', 'DHK')->first();

        // Close the seeded open shift first
        $openShift = CashierShift::where('user_id', $cashier->id)->where('status', 'open')->first();
        if ($openShift) {
            $openShift->update(['status' => 'closed', 'closing_cash_counted' => 25000.00]);
        }

        // 1. Open new shift
        $responseOpen = $this->actingAs($cashier)->post('/shifts/start', [
            'opening_cash' => 12000.00,
            'notes' => 'Evening desk shift',
        ]);

        $responseOpen->assertSessionHas('success');
        $shift = CashierShift::where('user_id', $cashier->id)->where('status', 'open')->first();
        $this->assertNotNull($shift);
        $this->assertEquals(12000.00, $shift->opening_cash);

        // 2. Prevent opening another shift concurrently
        $responseDuplicate = $this->actingAs($cashier)->post('/shifts/start', [
            'opening_cash' => 5000.00,
        ]);
        $responseDuplicate->assertSessionHas('error');

        // 3. Collect payment in active shift
        $reservation = Reservation::where('branch_id', $dhakaBranch->id)->where('status', 'checked_in')->first();
        $invoice = $reservation->latestInvoice;

        $responsePay = $this->actingAs($cashier)->post('/billing/payments', [
            'reservation_id' => $reservation->id,
            'invoice_id' => $invoice?->id,
            'amount' => 4500.00,
            'payment_method' => 'cash',
            'notes' => 'Front desk settlement',
        ]);
        $responsePay->assertSessionHas('success');

        // Verify shift transactions & expected cash
        $this->assertEquals(4500.00, $shift->fresh()->cash_collected);
        $expectedCash = $shift->fresh()->calculateExpectedCash();
        $this->assertEquals(12000.00 + 4500.00, $expectedCash);

        // 4. Close shift with counted cash (simulate 100 BDT overage)
        $countedCash = 16600.00;
        $responseClose = $this->actingAs($cashier)->post("/shifts/{$shift->id}/close", [
            'closing_cash_counted' => $countedCash,
            'discrepancy_reason' => 'Small guest change left in float',
        ]);

        $responseClose->assertRedirect();
        $closedShift = $shift->fresh();
        $this->assertEquals('closed', $closedShift->status);
        $this->assertEquals(100.00, $closedShift->cash_discrepancy);
    }
}
