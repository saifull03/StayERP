<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\CashierShift;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $activeBranchId = $request->attributes->get('active_branch_id');
        $reportType = $request->input('type', 'revenue');
        $startDate = $request->input('start_date', Carbon::today()->subDays(30)->toDateString());
        $endDate = $request->input('end_date', Carbon::today()->toDateString());

        // Branch filter
        $branchFilter = $activeBranchId ?? $request->input('branch_id');

        // Revenue metrics
        $paymentsQuery = Payment::where('status', 'completed')
            ->whereBetween('paid_at', ["{$startDate} 00:00:00", "{$endDate} 23:59:59"]);

        if ($branchFilter) {
            $paymentsQuery->where('branch_id', $branchFilter);
        }

        $totalRevenue = (float) (clone $paymentsQuery)->sum('amount');

        // Breakdown by method
        $methods = ['cash', 'card', 'bank_transfer', 'bkash', 'nagad', 'rocket'];
        $byMethod = [];
        foreach ($methods as $m) {
            $byMethod[$m] = (float) (clone $paymentsQuery)->where('payment_method', $m)->sum('amount');
        }

        // Expenses
        $expensesQuery = Expense::whereIn('status', ['approved', 'paid'])
            ->whereBetween('expense_date', [$startDate, $endDate]);

        if ($branchFilter) {
            $expensesQuery->where('branch_id', $branchFilter);
        }

        $totalExpenses = (float) (clone $expensesQuery)->sum('amount');

        // Reservations
        $reservationsQuery = Reservation::whereBetween('created_at', ["{$startDate} 00:00:00", "{$endDate} 23:59:59"]);
        if ($branchFilter) {
            $reservationsQuery->where('branch_id', $branchFilter);
        }
        $totalReservations = (clone $reservationsQuery)->count();
        $checkedInCount = (clone $reservationsQuery)->where('status', 'checked_in')->count();
        $checkedOutCount = (clone $reservationsQuery)->where('status', 'checked_out')->count();
        $cancelledCount = (clone $reservationsQuery)->where('status', 'cancelled')->count();

        // Branch Comparison (if Super Admin or all)
        $branches = Branch::where('is_active', true)->get();
        $branchPerformance = [];
        foreach ($branches as $b) {
            $bRev = (float) Payment::where('branch_id', $b->id)
                ->where('status', 'completed')
                ->whereBetween('paid_at', ["{$startDate} 00:00:00", "{$endDate} 23:59:59"])
                ->sum('amount');

            $bExp = (float) Expense::where('branch_id', $b->id)
                ->whereIn('status', ['approved', 'paid'])
                ->whereBetween('expense_date', [$startDate, $endDate])
                ->sum('amount');

            $bRooms = Room::where('branch_id', $b->id)->where('is_active', true)->count();

            $branchPerformance[] = [
                'branch' => $b,
                'rooms' => $bRooms,
                'revenue' => $bRev,
                'expenses' => $bExp,
                'net_income' => $bRev - $bExp,
            ];
        }

        // Shifts summary
        $shifts = CashierShift::when($branchFilter, fn($q) => $q->where('branch_id', $branchFilter))
            ->whereBetween('opened_at', ["{$startDate} 00:00:00", "{$endDate} 23:59:59"])
            ->with(['user', 'branch'])
            ->latest('opened_at')
            ->limit(10)
            ->get();

        return view('reports.index', compact(
            'reportType',
            'startDate',
            'endDate',
            'branchFilter',
            'branches',
            'totalRevenue',
            'totalExpenses',
            'byMethod',
            'totalReservations',
            'checkedInCount',
            'checkedOutCount',
            'cancelledCount',
            'branchPerformance',
            'shifts'
        ));
    }

    public function exportCsv(Request $request): Response
    {
        $activeBranchId = $request->attributes->get('active_branch_id');
        $type = $request->input('type', 'revenue');

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"stayinghotel_{$type}_report.csv\"",
        ];

        $callback = function () use ($type, $activeBranchId) {
            $file = fopen('php://output', 'w');

            if ($type === 'revenue') {
                fputcsv($file, ['Payment Number', 'Branch', 'Reservation', 'Amount (BDT)', 'Method', 'Reference', 'Date']);
                $payments = Payment::when($activeBranchId, fn($q) => $q->where('branch_id', $activeBranchId))
                    ->with(['branch', 'reservation'])
                    ->where('status', 'completed')
                    ->latest('paid_at')
                    ->get();

                foreach ($payments as $p) {
                    fputcsv($file, [
                        $p->payment_number,
                        $p->branch->name,
                        $p->reservation->reservation_number,
                        $p->amount,
                        strtoupper($p->payment_method),
                        $p->transaction_reference,
                        $p->paid_at->toDateTimeString(),
                    ]);
                }
            } elseif ($type === 'expenses') {
                fputcsv($file, ['Expense Number', 'Branch', 'Title', 'Category', 'Amount (BDT)', 'Status', 'Date']);
                $expenses = Expense::when($activeBranchId, fn($q) => $q->where('branch_id', $activeBranchId))
                    ->with(['branch', 'category'])
                    ->latest('expense_date')
                    ->get();

                foreach ($expenses as $e) {
                    fputcsv($file, [
                        $e->expense_number,
                        $e->branch->name,
                        $e->title,
                        $e->category->name,
                        $e->amount,
                        strtoupper($e->status),
                        $e->expense_date->toDateString(),
                    ]);
                }
            } else {
                fputcsv($file, ['Reservation Number', 'Branch', 'Guest', 'Check In', 'Check Out', 'Status', 'Total (BDT)']);
                $reservations = Reservation::when($activeBranchId, fn($q) => $q->where('branch_id', $activeBranchId))
                    ->with(['branch', 'guest'])
                    ->latest()
                    ->get();

                foreach ($reservations as $r) {
                    fputcsv($file, [
                        $r->reservation_number,
                        $r->branch->name,
                        $r->guest->full_name,
                        $r->check_in_date->toDateString(),
                        $r->check_out_date->toDateString(),
                        strtoupper($r->status),
                        $r->total_charges,
                    ]);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
