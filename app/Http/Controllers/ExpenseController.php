<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Branch;
use App\Models\Expense;
use App\Models\ExpenseApproval;
use App\Models\ExpenseCategory;
use App\Models\Vendor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function index(Request $request): View
    {
        $activeBranchId = $request->attributes->get('active_branch_id');
        $status = $request->input('status');

        $query = Expense::with(['category', 'vendor', 'branch', 'requestedBy', 'approvedBy'])->latest('expense_date');

        if ($activeBranchId) {
            $query->where('branch_id', $activeBranchId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $expenses = $query->paginate(15)->withQueryString();

        $categories = ExpenseCategory::where('is_active', true)
            ->where(function ($q) use ($activeBranchId) {
                $q->whereNull('branch_id');
                if ($activeBranchId) {
                    $q->orWhere('branch_id', $activeBranchId);
                }
            })
            ->orderBy('name')
            ->get();

        // Self-healing default categories if none exist
        if ($categories->isEmpty()) {
            $defaultCategories = [
                ['name' => 'Housekeeping Supplies', 'code' => 'EXP-HK', 'desc' => 'Detergents, linens, bath amenities'],
                ['name' => 'Utilities & Power', 'code' => 'EXP-UTIL', 'desc' => 'DESCO electricity, gas, water supply'],
                ['name' => 'Maintenance & Facility Repairs', 'code' => 'EXP-MAINT', 'desc' => 'HVAC, plumbing, electrical spares'],
                ['name' => 'Food & Beverage Provisions', 'code' => 'EXP-FB', 'desc' => 'Restaurant groceries, beverage stock'],
                ['name' => 'Stationery & Front Desk', 'code' => 'EXP-STAT', 'desc' => 'Keycards, folios, office stationery'],
                ['name' => 'Laundry & Linen Services', 'code' => 'EXP-LAUNDRY', 'desc' => 'External laundry and dry cleaning'],
                ['name' => 'Internet & IT Systems', 'code' => 'EXP-IT', 'desc' => 'Broadband, software subscriptions, POS hardware'],
                ['name' => 'Staff Welfare & Meals', 'code' => 'EXP-STAFF', 'desc' => 'Duty meals and staff refreshments'],
            ];

            foreach ($defaultCategories as $cat) {
                ExpenseCategory::firstOrCreate(
                    ['code' => $cat['code']],
                    [
                        'branch_id' => null,
                        'name' => $cat['name'],
                        'description' => $cat['desc'],
                        'is_active' => true,
                    ]
                );
            }

            $categories = ExpenseCategory::where('is_active', true)
                ->where(function ($q) use ($activeBranchId) {
                    $q->whereNull('branch_id');
                    if ($activeBranchId) {
                        $q->orWhere('branch_id', $activeBranchId);
                    }
                })
                ->orderBy('name')
                ->get();
        }

        $vendors = Vendor::where('is_active', true)
            ->where(function ($q) use ($activeBranchId) {
                $q->whereNull('branch_id');
                if ($activeBranchId) {
                    $q->orWhere('branch_id', $activeBranchId);
                }
            })
            ->orderBy('name')
            ->get();

        return view('expenses.index', compact('expenses', 'categories', 'vendors', 'status'));
    }

    public function store(Request $request): RedirectResponse
    {
        $activeBranchId = $request->attributes->get('active_branch_id') ?? Branch::first()->id;

        // If user submitted an inline new category name
        if ($request->filled('new_category_name') && empty($request->input('expense_category_id'))) {
            $newCatName = trim($request->input('new_category_name'));
            $cleanCode = 'EXP-' . strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $newCatName), 0, 5));
            $code = $cleanCode . '-' . rand(100, 999);
            while (ExpenseCategory::where('code', $code)->exists()) {
                $code = $cleanCode . '-' . rand(100, 999);
            }

            $newCat = ExpenseCategory::create([
                'branch_id' => null,
                'name' => $newCatName,
                'code' => $code,
                'description' => 'Added during expense entry',
                'is_active' => true,
            ]);

            $request->merge(['expense_category_id' => $newCat->id]);
        }

        $validated = $request->validate([
            'expense_category_id' => ['required', 'exists:expense_categories,id'],
            'vendor_id' => ['nullable', 'exists:vendors,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
            'amount' => ['required', 'numeric', 'min:1'],
            'payment_method' => ['required', 'in:cash,bank_transfer,card,mfs'],
            'expense_date' => ['required', 'date'],
        ]);

        $expense = Expense::create(array_merge($validated, [
            'branch_id' => $activeBranchId,
            'expense_number' => Expense::generateExpenseNumber($activeBranchId),
            'status' => 'pending_approval',
            'requested_by_user_id' => $request->user()->id,
        ]));

        AuditLog::log('expense.created', $expense, null, [
            'expense_number' => $expense->expense_number,
            'amount' => $expense->amount,
            'title' => $expense->title,
        ]);

        return back()->with('success', "Expense {$expense->expense_number} recorded and pending authorization.");
    }

    public function storeCategory(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'code' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $cleanCode = !empty($validated['code']) 
            ? strtoupper(trim($validated['code']))
            : 'EXP-' . strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $validated['name']), 0, 5));
        
        $code = $cleanCode;
        while (ExpenseCategory::where('code', $code)->exists()) {
            $code = $cleanCode . '-' . rand(10, 99);
        }

        ExpenseCategory::create([
            'branch_id' => null,
            'name' => $validated['name'],
            'code' => $code,
            'description' => $validated['description'] ?? null,
            'is_active' => true,
        ]);

        return back()->with('success', "Expense category '{$validated['name']}' created successfully.");
    }

    public function approve(Request $request, Expense $expense): RedirectResponse
    {
        $user = $request->user();

        if (!$user->hasPermission('expenses.approve', $expense->branch_id)) {
            abort(403, 'Unauthorized. You lack permission to approve expenses.');
        }

        // Prevent self-approval of expenses unless Super Admin
        if ($expense->requested_by_user_id === $user->id && !$user->isSuperAdmin()) {
            return back()->with('error', 'Segregation of duties: You cannot approve your own expense submission.');
        }

        $validated = $request->validate([
            'status' => ['required', 'in:approved,rejected'],
            'comments' => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($expense, $user, $validated) {
            $expense->update([
                'status' => $validated['status'],
                'approved_by_user_id' => $user->id,
            ]);

            ExpenseApproval::create([
                'expense_id' => $expense->id,
                'approver_user_id' => $user->id,
                'status' => $validated['status'],
                'comments' => $validated['comments'] ?? null,
                'approved_at' => now(),
            ]);

            AuditLog::log("expense.{$validated['status']}", $expense);
        });

        return back()->with('success', "Expense {$expense->expense_number} marked as {$validated['status']}.");
    }
}
