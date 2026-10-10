<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Payment;
use App\Models\Refund;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpenseAndRefundWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_expense_submission_and_segregation_of_duties(): void
    {
        $manager = User::where('email', 'manager.dhaka@stayinghotel.com')->first();
        $finance = User::where('email', 'finance@stayinghotel.com')->first();
        $dhakaBranch = Branch::where('code', 'DHK')->first();
        $category = ExpenseCategory::where('branch_id', $dhakaBranch->id)->first();

        // 1. Submit expense by Manager
        $responseCreate = $this->actingAs($manager)->post('/expenses', [
            'expense_category_id' => $category->id,
            'title' => 'Emergency Plumbing Spares',
            'amount' => 8500.00,
            'payment_method' => 'cash',
            'expense_date' => now()->toDateString(),
            'description' => 'Pipe couplings and sealant for 3rd floor',
        ]);
        $responseCreate->assertSessionHas('success');

        $expense = Expense::where('title', 'Emergency Plumbing Spares')->first();
        $this->assertNotNull($expense);
        $this->assertEquals('pending_approval', $expense->status);

        // 2. Segregation of duties: Manager cannot approve their own submission
        $responseSelfApprove = $this->actingAs($manager)->post("/expenses/{$expense->id}/approve", [
            'status' => 'approved',
        ]);
        $responseSelfApprove->assertSessionHas('error');
        $this->assertEquals('pending_approval', $expense->fresh()->status);

        // 3. Finance Manager approves the expense
        $responseApprove = $this->actingAs($finance)->post("/expenses/{$expense->id}/approve", [
            'status' => 'approved',
            'comments' => 'Approved as per emergency procurement budget',
        ]);
        $responseApprove->assertSessionHas('success');
        $this->assertEquals('approved', $expense->fresh()->status);
        $this->assertEquals($finance->id, $expense->fresh()->approved_by_user_id);
    }

    public function test_refund_workflow(): void
    {
        $cashier = User::where('email', 'cashier.dhaka@stayinghotel.com')->first();
        $finance = User::where('email', 'finance@stayinghotel.com')->first();
        $payment = Payment::where('status', 'completed')->first();

        // 1. Request refund
        $responseReq = $this->actingAs($cashier)->post('/billing/refunds/request', [
            'payment_id' => $payment->id,
            'amount' => 1000.00,
            'reason' => 'Guest room change fee adjustment',
        ]);
        $responseReq->assertSessionHas('success');

        $refund = Refund::where('payment_id', $payment->id)->first();
        $this->assertNotNull($refund);
        $this->assertEquals('pending_approval', $refund->status);

        // 2. Finance authorizes refund
        $responseApprove = $this->actingAs($finance)->post("/billing/refunds/{$refund->id}/approve", [
            'action' => 'approve',
        ]);
        $responseApprove->assertSessionHas('success');
        $this->assertEquals('processed', $refund->fresh()->status);
    }
}
