<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Branch;
use App\Models\CashierShift;
use App\Models\Expense;
use App\Models\HousekeepingTask;
use App\Models\Invoice;
use App\Models\MaintenanceRequest;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\Refund;
use App\Models\Reservation;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $primaryRole = $user->getPrimaryRole();
        $slug = $primaryRole ? $primaryRole->slug : 'receptionist';

        if ($user->isSuperAdmin()) {
            return $this->superAdminDashboard($request);
        }

        return match ($slug) {
            'super-admin' => $this->superAdminDashboard($request),
            'branch-manager' => $this->branchManagerDashboard($request),
            'finance-manager' => $this->financeManagerDashboard($request),
            'cashier' => $this->cashierDashboard($request),
            'receptionist' => $this->receptionistDashboard($request),
            'housekeeping' => $this->housekeepingDashboard($request),
            'maintenance' => $this->maintenanceDashboard($request),
            'auditor' => $this->auditorDashboard($request),
            default => $this->receptionistDashboard($request),
        };
    }

    public function superAdminDashboard(Request $request): View
    {
        $today = Carbon::today();
        $activeBranchId = $request->attributes->get('active_branch_id');

        // Query scope: all branches or selected branch
        $branchQuery = Branch::where('is_active', true);
        $branches = $branchQuery->get();

        $roomsQuery = Room::where('is_active', true);
        $reservationsQuery = Reservation::query();
        $paymentsQuery = Payment::where('status', 'completed');
        $expensesQuery = Expense::whereIn('status', ['approved', 'paid']);
        $invoicesQuery = Invoice::query();

        if ($activeBranchId) {
            $roomsQuery->where('branch_id', $activeBranchId);
            $reservationsQuery->where('branch_id', $activeBranchId);
            $paymentsQuery->where('branch_id', $activeBranchId);
            $expensesQuery->where('branch_id', $activeBranchId);
            $invoicesQuery->where('branch_id', $activeBranchId);
        }

        $totalBranches = Branch::count();
        $activeBranches = Branch::where('is_active', true)->count();
        $totalRooms = (clone $roomsQuery)->count();

        // Currently occupied rooms
        $occupiedRooms = (clone $reservationsQuery)
            ->where('status', 'checked_in')
            ->count();

        $occupancyRate = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100, 1) : 0;

        $totalReservations = (clone $reservationsQuery)->count();
        $totalRevenue = (float) (clone $paymentsQuery)->sum('amount');
        $totalExpenses = (float) (clone $expensesQuery)->sum('amount');
        $outstandingBalances = (float) (clone $invoicesQuery)->whereIn('status', ['unpaid', 'partially_paid'])->sum('balance_due');
        $netOperatingIncome = $totalRevenue - $totalExpenses;

        // Branch Comparison Cards
        $branchComparisons = [];
        foreach ($branches as $branch) {
            $bRooms = Room::where('branch_id', $branch->id)->where('is_active', true)->count();
            $bOccupied = Reservation::where('branch_id', $branch->id)->where('status', 'checked_in')->count();
            $bOccupancy = $bRooms > 0 ? round(($bOccupied / $bRooms) * 100, 1) : 0;
            $bRevenue = (float) Payment::where('branch_id', $branch->id)->where('status', 'completed')->sum('amount');
            $bExpenses = (float) Expense::where('branch_id', $branch->id)->whereIn('status', ['approved', 'paid'])->sum('amount');

            $branchComparisons[] = [
                'id' => $branch->id,
                'name' => $branch->name,
                'code' => $branch->code,
                'city' => $branch->city,
                'total_rooms' => $bRooms,
                'occupied_rooms' => $bOccupied,
                'occupancy_rate' => $bOccupancy,
                'revenue' => $bRevenue,
                'expenses' => $bExpenses,
                'net_income' => $bRevenue - $bExpenses,
            ];
        }

        // Sort by revenue descending for ranking
        usort($branchComparisons, fn($a, $b) => $b['revenue'] <=> $a['revenue']);

        $recentAudits = AuditLog::with(['user', 'branch'])
            ->latest('created_at')
            ->limit(8)
            ->get();

        $notifications = Notification::where('user_id', $request->user()->id)
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboards.super_admin', compact(
            'totalBranches',
            'activeBranches',
            'totalRooms',
            'occupiedRooms',
            'occupancyRate',
            'totalReservations',
            'totalRevenue',
            'totalExpenses',
            'outstandingBalances',
            'netOperatingIncome',
            'branchComparisons',
            'recentAudits',
            'notifications'
        ));
    }

    public function branchManagerDashboard(Request $request): View
    {
        $activeBranch = $request->attributes->get('active_branch');
        $branchId = $activeBranch?->id ?? Branch::first()?->id;
        $today = Carbon::today();

        $totalRooms = Room::where('branch_id', $branchId)->where('is_active', true)->count();
        $availableRooms = Room::where('branch_id', $branchId)
            ->where('operational_status', 'available')
            ->where('is_active', true)
            ->count();

        $occupiedRooms = Reservation::where('branch_id', $branchId)
            ->where('status', 'checked_in')
            ->count();

        $cleaningRooms = Room::where('branch_id', $branchId)
            ->where('operational_status', 'cleaning')
            ->count();

        $maintenanceRooms = Room::where('branch_id', $branchId)
            ->where('operational_status', 'maintenance')
            ->count();

        $todayArrivals = Reservation::where('branch_id', $branchId)
            ->whereDate('check_in_date', $today)
            ->whereIn('status', ['confirmed', 'pending'])
            ->count();

        $todayDepartures = Reservation::where('branch_id', $branchId)
            ->whereDate('check_out_date', $today)
            ->where('status', 'checked_in')
            ->count();

        $todayReservations = Reservation::where('branch_id', $branchId)
            ->whereDate('created_at', $today)
            ->count();

        $branchRevenue = (float) Payment::where('branch_id', $branchId)
            ->where('status', 'completed')
            ->sum('amount');

        $pendingHousekeeping = HousekeepingTask::where('branch_id', $branchId)
            ->whereIn('status', ['dirty', 'cleaning', 'awaiting_inspection'])
            ->with(['room', 'assignedTo'])
            ->latest()
            ->limit(5)
            ->get();

        $openMaintenance = MaintenanceRequest::where('branch_id', $branchId)
            ->whereIn('status', ['open', 'assigned', 'in_progress'])
            ->with(['room', 'assignedTo'])
            ->latest()
            ->limit(5)
            ->get();

        $recentReservations = Reservation::where('branch_id', $branchId)
            ->with(['guest', 'reservationRooms.room'])
            ->latest()
            ->limit(6)
            ->get();

        return view('dashboards.branch_manager', compact(
            'activeBranch',
            'totalRooms',
            'availableRooms',
            'occupiedRooms',
            'cleaningRooms',
            'maintenanceRooms',
            'todayArrivals',
            'todayDepartures',
            'todayReservations',
            'branchRevenue',
            'pendingHousekeeping',
            'openMaintenance',
            'recentReservations'
        ));
    }

    public function financeManagerDashboard(Request $request): View
    {
        $activeBranchId = $request->attributes->get('active_branch_id');

        $paymentsQuery = Payment::where('status', 'completed');
        $expensesQuery = Expense::query();
        $invoicesQuery = Invoice::query();

        if ($activeBranchId) {
            $paymentsQuery->where('branch_id', $activeBranchId);
            $expensesQuery->where('branch_id', $activeBranchId);
            $invoicesQuery->where('branch_id', $activeBranchId);
        }

        $totalPayments = (float) (clone $paymentsQuery)->sum('amount');

        // Revenue by Payment Method
        $methods = ['cash', 'card', 'bank_transfer', 'bkash', 'nagad', 'rocket'];
        $revenueByMethod = [];
        foreach ($methods as $m) {
            $revenueByMethod[$m] = (float) (clone $paymentsQuery)->where('payment_method', $m)->sum('amount');
        }

        $outstandingBalances = (float) (clone $invoicesQuery)->whereIn('status', ['unpaid', 'partially_paid'])->sum('balance_due');

        $refundsTotal = (float) Refund::when($activeBranchId, fn($q) => $q->where('branch_id', $activeBranchId))
            ->where('status', 'processed')
            ->sum('amount');

        $approvedExpenses = (float) (clone $expensesQuery)->whereIn('status', ['approved', 'paid'])->sum('amount');
        $pendingExpenses = (float) (clone $expensesQuery)->where('status', 'pending_approval')->sum('amount');

        // Recent Invoices
        $recentInvoices = (clone $invoicesQuery)
            ->with(['reservation.guest', 'branch'])
            ->latest()
            ->limit(6)
            ->get();

        // Recent Payments
        $recentPayments = (clone $paymentsQuery)
            ->with(['reservation.guest', 'receivedBy', 'branch'])
            ->latest('paid_at')
            ->limit(6)
            ->get();

        return view('dashboards.finance', compact(
            'totalPayments',
            'revenueByMethod',
            'outstandingBalances',
            'refundsTotal',
            'approvedExpenses',
            'pendingExpenses',
            'recentInvoices',
            'recentPayments'
        ));
    }

    public function cashierDashboard(Request $request): View
    {
        $user = $request->user();
        $activeBranch = $request->attributes->get('active_branch');
        $branchId = $activeBranch?->id ?? Branch::first()?->id;

        // Current open shift for this user
        $currentShift = CashierShift::where('user_id', $user->id)
            ->where('branch_id', $branchId)
            ->where('status', 'open')
            ->latest()
            ->first();

        $openingCash = $currentShift ? (float) $currentShift->opening_cash : 0.00;
        $cashCollected = $currentShift ? $currentShift->cash_collected : 0.00;
        $nonCashCollected = $currentShift ? $currentShift->non_cash_collected : 0.00;
        $expectedClosingCash = $currentShift ? $currentShift->calculateExpectedCash() : 0.00;

        // Recent payments in this shift
        $shiftPayments = $currentShift ? $currentShift->payments()->with(['reservation.guest'])->latest()->limit(8)->get() : collect();

        // Pending invoices in branch
        $pendingInvoices = Invoice::where('branch_id', $branchId)
            ->whereIn('status', ['unpaid', 'partially_paid'])
            ->with(['reservation.guest'])
            ->latest()
            ->limit(10)
            ->get();

        return view('dashboards.cashier', compact(
            'activeBranch',
            'currentShift',
            'openingCash',
            'cashCollected',
            'nonCashCollected',
            'expectedClosingCash',
            'shiftPayments',
            'pendingInvoices'
        ));
    }

    public function receptionistDashboard(Request $request): View
    {
        $activeBranch = $request->attributes->get('active_branch');
        $branchId = $activeBranch?->id ?? Branch::first()?->id;
        $today = Carbon::today();

        // Today's Expected Arrivals
        $todayArrivals = Reservation::where('branch_id', $branchId)
            ->whereDate('check_in_date', $today)
            ->whereIn('status', ['confirmed', 'pending'])
            ->with(['guest', 'reservationRooms.room', 'reservationRooms.roomType'])
            ->get();

        // Today's Expected Departures
        $todayDepartures = Reservation::where('branch_id', $branchId)
            ->whereDate('check_out_date', $today)
            ->where('status', 'checked_in')
            ->with(['guest', 'reservationRooms.room'])
            ->get();

        // Currently Checked In
        $checkedInGuests = Reservation::where('branch_id', $branchId)
            ->where('status', 'checked_in')
            ->with(['guest', 'reservationRooms.room'])
            ->latest('checked_in_at')
            ->limit(8)
            ->get();

        // Live Room Rack
        $rooms = Room::where('branch_id', $branchId)
            ->where('is_active', true)
            ->with('roomType')
            ->orderBy('floor')
            ->orderBy('room_number')
            ->get();

        return view('dashboards.receptionist', compact(
            'activeBranch',
            'todayArrivals',
            'todayDepartures',
            'checkedInGuests',
            'rooms'
        ));
    }

    public function housekeepingDashboard(Request $request): View
    {
        $activeBranch = $request->attributes->get('active_branch');
        $branchId = $activeBranch?->id ?? Branch::first()?->id;

        $tasks = HousekeepingTask::where('branch_id', $branchId)
            ->with(['room.roomType', 'assignedTo', 'inspectedBy'])
            ->latest()
            ->get();

        $dirtyCount = $tasks->where('status', 'dirty')->count();
        $cleaningCount = $tasks->where('status', 'cleaning')->count();
        $inspectCount = $tasks->where('status', 'awaiting_inspection')->count();
        $cleanCount = $tasks->where('status', 'clean')->count();

        // Rooms overview
        $rooms = Room::where('branch_id', $branchId)->where('is_active', true)->with('roomType')->get();

        return view('dashboards.housekeeping', compact(
            'activeBranch',
            'tasks',
            'dirtyCount',
            'cleaningCount',
            'inspectCount',
            'cleanCount',
            'rooms'
        ));
    }

    public function maintenanceDashboard(Request $request): View
    {
        $activeBranch = $request->attributes->get('active_branch');
        $branchId = $activeBranch?->id ?? Branch::first()?->id;

        $requests = MaintenanceRequest::where('branch_id', $branchId)
            ->with(['room', 'reportedBy', 'assignedTo'])
            ->latest()
            ->get();

        $openCount = $requests->where('status', 'open')->count();
        $assignedCount = $requests->where('status', 'assigned')->count();
        $inProgressCount = $requests->where('status', 'in_progress')->count();
        $resolvedCount = $requests->where('status', 'resolved')->count();
        $blockedRoomsCount = $requests->where('blocks_room', true)->whereIn('status', ['open', 'assigned', 'in_progress'])->count();

        return view('dashboards.maintenance', compact(
            'activeBranch',
            'requests',
            'openCount',
            'assignedCount',
            'inProgressCount',
            'resolvedCount',
            'blockedRoomsCount'
        ));
    }

    public function auditorDashboard(Request $request): View
    {
        $activeBranchId = $request->attributes->get('active_branch_id');

        $audits = AuditLog::when($activeBranchId, fn($q) => $q->where('branch_id', $activeBranchId))
            ->with(['user', 'branch'])
            ->latest('created_at')
            ->paginate(15);

        return view('dashboards.auditor', compact('audits'));
    }
}
