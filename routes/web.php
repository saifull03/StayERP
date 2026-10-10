<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\BranchSwitchController;
use App\Http\Controllers\CashierShiftController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\HousekeepingController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\AuditLogController;
use Illuminate\Support\Facades\Route;

// Guest Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/switch-branch', [BranchSwitchController::class, 'switchBranch'])->name('branch.switch');

    // Root & Dynamic Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('home');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Role-specific Direct Views
    Route::get('/dashboard/super-admin', [DashboardController::class, 'superAdminDashboard'])
        ->middleware('role:super-admin')
        ->name('dashboard.super-admin');

    Route::get('/dashboard/branch-manager', [DashboardController::class, 'branchManagerDashboard'])
        ->middleware('role:super-admin,branch-manager')
        ->name('dashboard.branch-manager');

    Route::get('/dashboard/finance', [DashboardController::class, 'financeManagerDashboard'])
        ->middleware('role:super-admin,finance-manager')
        ->name('dashboard.finance');

    Route::get('/dashboard/cashier', [DashboardController::class, 'cashierDashboard'])
        ->middleware('role:super-admin,branch-manager,cashier')
        ->name('dashboard.cashier');

    Route::get('/dashboard/receptionist', [DashboardController::class, 'receptionistDashboard'])
        ->middleware('role:super-admin,branch-manager,receptionist')
        ->name('dashboard.receptionist');

    Route::get('/dashboard/housekeeping', [DashboardController::class, 'housekeepingDashboard'])
        ->middleware('role:super-admin,branch-manager,housekeeping')
        ->name('dashboard.housekeeping');

    Route::get('/dashboard/maintenance', [DashboardController::class, 'maintenanceDashboard'])
        ->middleware('role:super-admin,branch-manager,maintenance')
        ->name('dashboard.maintenance');

    Route::get('/dashboard/auditor', [DashboardController::class, 'auditorDashboard'])
        ->middleware('role:super-admin,auditor')
        ->name('dashboard.auditor');

    // Reservations & Guest Stay Management
    Route::prefix('reservations')->name('reservations.')->group(function () {
        Route::get('/', [ReservationController::class, 'index'])->name('index')->middleware('permission:reservations.view');
        Route::get('/create', [ReservationController::class, 'create'])->name('create')->middleware('permission:reservations.create');
        Route::post('/', [ReservationController::class, 'store'])->name('store')->middleware('permission:reservations.create');
        Route::get('/{reservation}', [ReservationController::class, 'show'])->name('show')->middleware('permission:reservations.view');
        Route::post('/{reservation}/check-in', [ReservationController::class, 'checkIn'])->name('checkin')->middleware('permission:reservations.checkin');
        Route::post('/{reservation}/check-out', [ReservationController::class, 'checkOut'])->name('checkout')->middleware('permission:reservations.checkout');
        Route::post('/{reservation}/cancel', [ReservationController::class, 'cancel'])->name('cancel')->middleware('permission:reservations.cancel');
        Route::post('/{reservation}/folio-item', [ReservationController::class, 'addFolioItem'])->name('folio.add')->middleware('permission:reservations.update');
    });

    // Rooms & Availability Calendar
    Route::prefix('rooms')->name('rooms.')->group(function () {
        Route::get('/', [RoomController::class, 'index'])->name('index')->middleware('permission:rooms.view');
        Route::get('/calendar', [RoomController::class, 'calendar'])->name('calendar')->middleware('permission:rooms.view');
        Route::post('/{room}/status', [RoomController::class, 'updateStatus'])->name('status.update')->middleware('permission:rooms.manage');
    });

    // Guest Directory
    Route::prefix('guests')->name('guests.')->group(function () {
        Route::get('/', [GuestController::class, 'index'])->name('index')->middleware('permission:guests.view');
        Route::get('/search', [GuestController::class, 'search'])->name('search')->middleware('permission:guests.view,reservations.create');
        Route::post('/', [GuestController::class, 'store'])->name('store')->middleware('permission:guests.manage');
        Route::get('/{guest}', [GuestController::class, 'show'])->name('show')->middleware('permission:guests.view');
    });

    // Billing, Payments, Invoices & Receipts
    Route::prefix('billing')->name('billing.')->group(function () {
        Route::get('/invoices', [BillingController::class, 'invoices'])->name('invoices')->middleware('permission:payments.view');
        Route::get('/invoices/{invoice}', [BillingController::class, 'showInvoice'])->name('invoice.show')->middleware('permission:payments.view');
        Route::get('/payments', [BillingController::class, 'payments'])->name('payments')->middleware('permission:payments.view');
        Route::post('/payments', [BillingController::class, 'recordPayment'])->name('payment.record')->middleware('permission:payments.collect');
        Route::get('/payments/{payment}/receipt', [BillingController::class, 'printReceipt'])->name('payment.receipt')->middleware('permission:payments.view');
        Route::post('/refunds/request', [BillingController::class, 'requestRefund'])->name('refund.request')->middleware('permission:payments.collect');
        Route::post('/refunds/{refund}/approve', [BillingController::class, 'approveRefund'])->name('refund.approve')->middleware('permission:payments.refund');
    });

    // Cashier Shifts & Cash Reconciliation
    Route::prefix('shifts')->name('shifts.')->group(function () {
        Route::get('/', [CashierShiftController::class, 'index'])->name('index')->middleware('permission:shifts.manage');
        Route::post('/start', [CashierShiftController::class, 'startShift'])->name('start')->middleware('permission:shifts.manage');
        Route::post('/{shift}/close', [CashierShiftController::class, 'closeShift'])->name('close')->middleware('permission:shifts.manage');
        Route::get('/{shift}', [CashierShiftController::class, 'show'])->name('show')->middleware('permission:shifts.manage');
    });

    // Expenses & Vendor Management
    Route::prefix('expenses')->name('expenses.')->group(function () {
        Route::get('/', [ExpenseController::class, 'index'])->name('index')->middleware('permission:finance.reports');
        Route::post('/', [ExpenseController::class, 'store'])->name('store')->middleware('permission:expenses.create');
        Route::post('/categories', [ExpenseController::class, 'storeCategory'])->name('categories.store')->middleware('permission:expenses.create');
        Route::post('/{expense}/approve', [ExpenseController::class, 'approve'])->name('approve')->middleware('permission:expenses.approve');
    });

    // Housekeeping Operations
    Route::prefix('housekeeping')->name('housekeeping.')->group(function () {
        Route::get('/', [HousekeepingController::class, 'index'])->name('index')->middleware('permission:housekeeping.manage');
        Route::post('/', [HousekeepingController::class, 'store'])->name('store')->middleware('permission:housekeeping.manage');
        Route::post('/{task}/status', [HousekeepingController::class, 'updateStatus'])->name('status.update')->middleware('permission:housekeeping.manage');
    });

    // Maintenance Operations
    Route::prefix('maintenance')->name('maintenance.')->group(function () {
        Route::get('/', [MaintenanceController::class, 'index'])->name('index')->middleware('permission:maintenance.manage');
        Route::post('/', [MaintenanceController::class, 'store'])->name('store')->middleware('permission:maintenance.manage');
        Route::post('/{maintenance}/status', [MaintenanceController::class, 'updateStatus'])->name('status.update')->middleware('permission:maintenance.manage');
    });

    // Staff Management
    Route::prefix('staff')->name('staff.')->middleware('permission:staff.manage')->group(function () {
        Route::get('/', [StaffController::class, 'index'])->name('index');
        Route::post('/', [StaffController::class, 'store'])->name('store');
        Route::post('/{user}/toggle', [StaffController::class, 'toggleActive'])->name('toggle');
    });

    // Branch Management (Super Admin)
    Route::prefix('branches')->name('branches.')->middleware('role:super-admin')->group(function () {
        Route::get('/', [BranchController::class, 'index'])->name('index');
        Route::post('/', [BranchController::class, 'store'])->name('store');
        Route::post('/{branch}/toggle', [BranchController::class, 'toggleStatus'])->name('toggle');
    });

    // Reports & Analytics
    Route::prefix('reports')->name('reports.')->middleware('permission:finance.reports')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/export', [ReportController::class, 'exportCsv'])->name('export');
    });

    // Audit Logs
    Route::get('/audit-logs', [AuditLogController::class, 'index'])
        ->name('audit-logs.index')
        ->middleware('permission:audit_logs.view');

    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::post('/{notification}/read', [NotificationController::class, 'markRead'])->name('read');
        Route::post('/read-all', [NotificationController::class, 'markAllRead'])->name('read-all');
    });
});
