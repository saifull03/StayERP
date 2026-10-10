<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Branch;
use App\Models\CashierShift;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CashierShiftController extends Controller
{
    public function index(Request $request): View
    {
        $activeBranchId = $request->attributes->get('active_branch_id');
        $user = $request->user();

        $query = CashierShift::with(['user', 'branch', 'reviewedBy'])->latest('opened_at');

        if ($activeBranchId) {
            $query->where('branch_id', $activeBranchId);
        }

        if (!$user->hasRole(['super-admin', 'branch-manager', 'finance-manager'], $activeBranchId)) {
            $query->where('user_id', $user->id);
        }

        $shifts = $query->paginate(15);

        $currentOpenShift = CashierShift::where('user_id', $user->id)
            ->where('status', 'open')
            ->first();

        return view('shifts.index', compact('shifts', 'currentOpenShift'));
    }

    public function startShift(Request $request): RedirectResponse
    {
        $user = $request->user();
        $activeBranch = $request->attributes->get('active_branch') ?? Branch::first();

        // Check if cashier already has an open shift in this branch
        $existing = CashierShift::where('user_id', $user->id)
            ->where('status', 'open')
            ->first();

        if ($existing) {
            return back()->with('error', "You already have an active open shift ({$existing->shift_number}). Please close it before starting a new one.");
        }

        $validated = $request->validate([
            'opening_cash' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $shift = CashierShift::create([
            'branch_id' => $activeBranch->id,
            'user_id' => $user->id,
            'shift_number' => CashierShift::generateShiftNumber($activeBranch->id),
            'opening_cash' => $validated['opening_cash'],
            'status' => 'open',
            'opened_at' => now(),
            'notes' => $validated['notes'] ?? null,
        ]);

        AuditLog::log('shift.opened', $shift, null, [
            'shift_number' => $shift->shift_number,
            'opening_cash' => $shift->opening_cash,
        ]);

        return back()->with('success', "Cashier shift {$shift->shift_number} opened with float of BDT " . number_format($shift->opening_cash, 2));
    }

    public function closeShift(Request $request, CashierShift $shift): RedirectResponse
    {
        $user = $request->user();

        if ($shift->user_id !== $user->id && !$user->hasRole(['super-admin', 'branch-manager'], $shift->branch_id)) {
            abort(403, 'Unauthorized to close this cashier shift.');
        }

        if ($shift->status !== 'open') {
            return back()->with('error', 'This shift is already closed.');
        }

        $validated = $request->validate([
            'closing_cash_counted' => ['required', 'numeric', 'min:0'],
            'discrepancy_reason' => ['nullable', 'string', 'max:500'],
        ]);

        $counted = (float) $validated['closing_cash_counted'];
        $expected = $shift->calculateExpectedCash();
        $discrepancy = round($counted - $expected, 2);

        $shift->update([
            'closing_cash_counted' => $counted,
            'expected_closing_cash' => $expected,
            'cash_discrepancy' => $discrepancy,
            'discrepancy_reason' => $discrepancy != 0 ? ($validated['discrepancy_reason'] ?? 'Cash variance detected upon shift closing.') : null,
            'status' => 'closed',
            'closed_at' => now(),
        ]);

        AuditLog::log('shift.closed', $shift, null, [
            'shift_number' => $shift->shift_number,
            'counted' => $counted,
            'expected' => $expected,
            'discrepancy' => $discrepancy,
        ]);

        return redirect()->route('shifts.show', $shift)
            ->with('success', "Shift {$shift->shift_number} successfully closed and reconciled.");
    }

    public function show(CashierShift $shift): View
    {
        $shift->load([
            'user',
            'branch.organization',
            'reviewedBy',
            'payments.reservation.guest',
            'transactions',
            'expenses',
        ]);

        return view('shifts.show', compact('shift'));
    }
}
