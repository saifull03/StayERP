<?php

namespace App\Http\Middleware;

use App\Models\Branch;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class EnforceBranchContext
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        // Available branches for this user
        if ($user->isSuperAdmin()) {
            $authorizedBranches = Branch::where('is_active', true)->get();
        } else {
            $branchIds = $user->branchAssignments()
                ->whereNotNull('branch_id')
                ->pluck('branch_id')
                ->unique();

            $authorizedBranches = Branch::whereIn('id', $branchIds)
                ->where('is_active', true)
                ->get();
        }

        // Active branch in session
        $sessionBranchId = session('active_branch_id');

        if ($sessionBranchId !== null && $sessionBranchId !== 'all') {
            $branchId = (int) $sessionBranchId;
            // Validate user has permission to this branch
            if (!$user->canAccessBranch($branchId)) {
                // Reset to primary branch
                $primary = $user->getPrimaryBranch();
                $sessionBranchId = $primary?->id ?? ($authorizedBranches->first()?->id);
                session(['active_branch_id' => $sessionBranchId]);
            }
        } elseif ($sessionBranchId === 'all') {
            if (!$user->isSuperAdmin()) {
                $primary = $user->getPrimaryBranch();
                $sessionBranchId = $primary?->id ?? ($authorizedBranches->first()?->id);
                session(['active_branch_id' => $sessionBranchId]);
            }
        } else {
            // No session branch set, initialize
            if ($user->isSuperAdmin()) {
                // Super Admin can default to first branch or all
                $sessionBranchId = $authorizedBranches->first()?->id ?? 'all';
            } else {
                $primary = $user->getPrimaryBranch();
                $sessionBranchId = $primary?->id ?? ($authorizedBranches->first()?->id);
            }
            session(['active_branch_id' => $sessionBranchId]);
        }

        $activeBranch = null;
        if ($sessionBranchId !== 'all' && $sessionBranchId !== null) {
            $activeBranch = $authorizedBranches->firstWhere('id', (int) $sessionBranchId) 
                ?? Branch::find((int) $sessionBranchId);
        }

        // Share globally with all views and request attributes
        $request->attributes->set('active_branch', $activeBranch);
        $request->attributes->set('active_branch_id', $activeBranch?->id);
        $request->attributes->set('authorized_branches', $authorizedBranches);

        View::share('activeBranch', $activeBranch);
        View::share('activeBranchId', $activeBranch?->id);
        View::share('authorizedBranches', $authorizedBranches);
        View::share('currentUser', $user);

        return $next($request);
    }
}
