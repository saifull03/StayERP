<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Branch;
use App\Models\CashierShift;
use App\Models\CashierTransaction;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Refund;
use App\Models\Reservation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BillingController extends Controller
{
    public function invoices(Request $request): View
    {
        $activeBranchId = $request->attributes->get('active_branch_id');
        $status = $request->input('status');
        $search = $request->input('search');

        $query = Invoice::with(['reservation.guest', 'branch', 'payments'])->latest();

        if ($activeBranchId) {
            $query->where('branch_id', $activeBranchId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('reservation.guest', function ($q) use ($search) {
                      $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                  });
        }

        $invoices = $query->paginate(15)->withQueryString();

        return view('billing.invoices', compact('invoices', 'status', 'search'));
    }

    public function showInvoice(Invoice $invoice): View
    {
        $invoice->load([
            'branch.organization',
            'reservation.guest',
            'reservation.reservationRooms.room.roomType',
            'items',
            'payments.receivedBy',
        ]);

        return view('billing.invoice_show', compact('invoice'));
    }

    public function payments(Request $request): View
    {
        $activeBranchId = $request->attributes->get('active_branch_id');
        $method = $request->input('method');
        $search = $request->input('search');

        $query = Payment::with(['reservation.guest', 'invoice', 'branch', 'receivedBy'])->latest('paid_at');

        if ($activeBranchId) {
            $query->where('branch_id', $activeBranchId);
        }

        if ($method) {
            $query->where('payment_method', $method);
        }

        if ($search) {
            $query->where('payment_number', 'like', "%{$search}%")
                  ->orWhere('transaction_reference', 'like', "%{$search}%")
                  ->orWhereHas('reservation.guest', function ($q) use ($search) {
                      $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                  });
        }

        $payments = $query->paginate(15)->withQueryString();

        return view('billing.payments', compact('payments', 'method', 'search'));
    }

    public function recordPayment(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'reservation_id' => ['required', 'exists:reservations,id'],
            'invoice_id' => ['nullable', 'exists:invoices,id'],
            'amount' => ['required', 'numeric', 'min:1'],
            'payment_method' => ['required', 'in:cash,card,bank_transfer,bkash,nagad,rocket,other'],
            'transaction_reference' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $reservation = Reservation::findOrFail($validated['reservation_id']);
        $user = $request->user();

        // Check if user has an open cashier shift in this branch
        $activeShift = CashierShift::where('user_id', $user->id)
            ->where('branch_id', $reservation->branch_id)
            ->where('status', 'open')
            ->first();

        return DB::transaction(function () use ($validated, $reservation, $user, $activeShift) {
            $invoice = isset($validated['invoice_id']) 
                ? Invoice::find($validated['invoice_id']) 
                : $reservation->latestInvoice;

            $payment = Payment::create([
                'branch_id' => $reservation->branch_id,
                'reservation_id' => $reservation->id,
                'invoice_id' => $invoice?->id,
                'cashier_shift_id' => $activeShift?->id,
                'payment_number' => Payment::generatePaymentNumber($reservation->branch_id),
                'amount' => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'transaction_reference' => $validated['transaction_reference'] ?? ('REF-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6))),
                'status' => 'completed',
                'notes' => $validated['notes'] ?? null,
                'received_by_user_id' => $user->id,
                'paid_at' => now(),
            ]);

            // If collected in open cashier shift
            if ($activeShift && $validated['payment_method'] === 'cash') {
                CashierTransaction::create([
                    'cashier_shift_id' => $activeShift->id,
                    'branch_id' => $reservation->branch_id,
                    'transaction_type' => 'payment_received',
                    'amount' => $validated['amount'],
                    'payment_id' => $payment->id,
                    'reference_number' => $payment->payment_number,
                    'notes' => "Cash collected for Reservation {$reservation->reservation_number}",
                ]);
            }

            if ($invoice) {
                $invoice->recalculateTotals();
            }

            AuditLog::log('payment.recorded', $payment, null, [
                'payment_number' => $payment->payment_number,
                'amount' => $payment->amount,
                'method' => $payment->payment_method,
                'reservation' => $reservation->reservation_number,
            ]);

            return back()->with('success', "Payment {$payment->payment_number} of BDT " . number_format($payment->amount, 2) . " successfully recorded.");
        });
    }

    public function printReceipt(Payment $payment): View
    {
        $payment->load([
            'branch.organization',
            'reservation.guest',
            'reservation.reservationRooms.room',
            'invoice',
            'receivedBy',
        ]);

        return view('billing.receipt_print', compact('payment'));
    }

    public function requestRefund(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'payment_id' => ['required', 'exists:payments,id'],
            'amount' => ['required', 'numeric', 'min:1'],
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $payment = Payment::with('reservation')->findOrFail($validated['payment_id']);

        if ($validated['amount'] > $payment->amount) {
            return back()->with('error', "Refund amount cannot exceed payment amount of BDT {$payment->amount}.");
        }

        $refund = Refund::create([
            'branch_id' => $payment->branch_id,
            'payment_id' => $payment->id,
            'reservation_id' => $payment->reservation_id,
            'refund_number' => Refund::generateRefundNumber($payment->branch_id),
            'amount' => $validated['amount'],
            'reason' => $validated['reason'],
            'status' => 'pending_approval',
        ]);

        AuditLog::log('refund.requested', $refund, null, ['amount' => $refund->amount, 'reason' => $refund->reason]);

        return back()->with('success', "Refund request {$refund->refund_number} submitted for manager authorization.");
    }

    public function approveRefund(Request $request, Refund $refund): RedirectResponse
    {
        $user = $request->user();
        if (!$user->hasPermission('payments.refund', $refund->branch_id)) {
            abort(403, 'Unauthorized to approve refunds.');
        }

        $action = $request->input('action', 'approve');

        if ($action === 'approve') {
            DB::transaction(function () use ($refund, $user) {
                $refund->update([
                    'status' => 'approved',
                    'approved_by_user_id' => $user->id,
                    'approved_at' => now(),
                ]);

                // Record cashier transaction if cash refund
                $activeShift = CashierShift::where('user_id', $user->id)
                    ->where('branch_id', $refund->branch_id)
                    ->where('status', 'open')
                    ->first();

                if ($activeShift && $refund->payment->payment_method === 'cash') {
                    CashierTransaction::create([
                        'cashier_shift_id' => $activeShift->id,
                        'branch_id' => $refund->branch_id,
                        'transaction_type' => 'refund_paid',
                        'amount' => $refund->amount,
                        'reference_number' => $refund->refund_number,
                        'notes' => "Refund paid for payment {$refund->payment->payment_number}",
                    ]);
                }

                $refund->update([
                    'status' => 'processed',
                    'processed_by_user_id' => $user->id,
                ]);

                if ($refund->payment->invoice) {
                    $refund->payment->invoice->recalculateTotals();
                }

                AuditLog::log('refund.approved', $refund);
            });

            return back()->with('success', "Refund {$refund->refund_number} authorized and processed.");
        } else {
            $refund->update(['status' => 'rejected']);
            AuditLog::log('refund.rejected', $refund);
            return back()->with('success', "Refund {$refund->refund_number} rejected.");
        }
    }
}
