<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BranchSwitchController extends Controller
{
    public function switchBranch(Request $request): RedirectResponse
    {
        $user = $request->user();
        $targetBranchId = $request->input('branch_id');

        if ($targetBranchId === 'all') {
            if (!$user->isSuperAdmin()) {
                return back()->with('error', 'Only Super Admin can view consolidated organization-wide data.');
            }
            session(['active_branch_id' => 'all']);
            return back()->with('success', 'Switched context to All Branches (Consolidated View).');
        }

        $branchId = (int) $targetBranchId;
        $branch = Branch::where('id', $branchId)->where('is_active', true)->first();

        if (!$branch) {
            return back()->with('error', 'Selected hotel branch not found or inactive.');
        }

        if (!$user->canAccessBranch($branchId)) {
            return back()->with('error', 'You are not authorized to access this hotel branch.');
        }

        session(['active_branch_id' => $branchId]);

        return back()->with('success', "Active branch switched to {$branch->name}.");
    }
}
