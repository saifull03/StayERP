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

        $categories = ExpenseCategory::when($activeBranchId, fn($q) => $q->where('branch_id', $activeBranchId))
            ->where('is_active', true)
            ->get();

        $vendors = Vendor::when($activeBranchId, fn($q) => $q->where('branch_id', $activeBranchId))
            ->where('is_active', true)
            ->get();

        return view('expenses.index', compact('expenses', 'categories', 'vendors', 'status'));
    }

    public function store(Request $request): RedirectResponse
    {
        $activeBranchId = $request->attributes->get('active_branch_id') ?? Branch::first()->id;

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
