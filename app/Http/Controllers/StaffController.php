<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Branch;
use App\Models\Role;
use App\Models\User;
use App\Models\UserBranchAssignment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class StaffController extends Controller
{
    public function index(Request $request): View
    {
        $activeBranchId = $request->attributes->get('active_branch_id');
        $user = $request->user();

        $query = User::with(['branchAssignments.branch', 'branchAssignments.role', 'organization'])->latest();

        if (!$user->isSuperAdmin() && $activeBranchId) {
            $query->whereHas('branchAssignments', fn($q) => $q->where('branch_id', $activeBranchId));
        }

        $staffMembers = $query->paginate(15);
        $branches = Branch::where('is_active', true)->get();
        $roles = Role::all();

        return view('staff.index', compact('staffMembers', 'branches', 'roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $currentUser = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:30'],
            'designation' => ['required', 'string', 'max:100'],
            'password' => ['required', 'string', 'min:8'],
            'role_id' => ['required', 'exists:roles,id'],
            'branch_id' => ['nullable', 'exists:branches,id'],
        ]);

        $targetRole = Role::findOrFail($validated['role_id']);

        // Prevent privilege escalation
        if (!$currentUser->isSuperAdmin() && $targetRole->slug === 'super-admin') {
            abort(403, 'Unauthorized to assign Super Admin privileges.');
        }

        DB::transaction(function () use ($validated, $currentUser) {
            $user = User::create([
                'organization_id' => $currentUser->organization_id,
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'designation' => $validated['designation'],
                'password' => Hash::make($validated['password']),
                'is_active' => true,
            ]);

            UserBranchAssignment::create([
                'user_id' => $user->id,
                'branch_id' => $validated['branch_id'] ?? null,
                'role_id' => $validated['role_id'],
                'is_primary' => true,
            ]);

            AuditLog::log('staff.created', $user);
        });

        return back()->with('success', "Employee {$validated['name']} registered successfully.");
    }

    public function toggleActive(User $user): RedirectResponse
    {
        $currentUser = auth()->user();

        if ($user->id === $currentUser->id) {
            return back()->with('error', 'You cannot deactivate your own account.');
        }

        if ($user->isSuperAdmin() && !$currentUser->isSuperAdmin()) {
            abort(403, 'Unauthorized to alter Super Admin status.');
        }

        $user->update(['is_active' => !$user->is_active]);

        AuditLog::log('staff.status_toggled', $user, null, ['is_active' => $user->is_active]);

        $statusText = $user->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Account for {$user->name} has been {$statusText}.");
    }
}
