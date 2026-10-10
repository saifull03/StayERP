<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Branch;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BranchController extends Controller
{
    public function index(): View
    {
        $branches = Branch::withCount(['rooms', 'reservations', 'userAssignments'])->latest()->get();
        return view('branches.index', compact('branches'));
    }

    public function store(Request $request): RedirectResponse
    {
        $org = Organization::first();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:10', 'unique:branches,code'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:100'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'tax_rate_percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'checkin_time' => ['nullable', 'date_format:H:i'],
            'checkout_time' => ['nullable', 'date_format:H:i'],
        ]);

        $branch = Branch::create(array_merge($validated, [
            'organization_id' => $org->id,
            'timezone' => 'Asia/Dhaka',
            'currency' => 'BDT',
            'is_active' => true,
        ]));

        AuditLog::log('branch.created', $branch);

        return back()->with('success', "Branch {$branch->name} ({$branch->code}) registered successfully.");
    }

    public function toggleStatus(Branch $branch): RedirectResponse
    {
        $branch->update(['is_active' => !$branch->is_active]);

        AuditLog::log('branch.status_toggled', $branch, null, ['is_active' => $branch->is_active]);

        $statusText = $branch->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Branch {$branch->name} has been {$statusText}.");
    }
}
