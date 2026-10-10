<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'organization_id',
        'name',
        'email',
        'phone',
        'designation',
        'is_active',
        'password',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function branchAssignments(): HasMany
    {
        return $this->hasMany(UserBranchAssignment::class);
    }

    public function branches(): BelongsToMany
    {
        return $this->belongsToMany(Branch::class, 'user_branch_assignments')
            ->withPivot(['role_id', 'is_primary'])
            ->withTimestamps();
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_branch_assignments')
            ->withPivot(['branch_id', 'is_primary'])
            ->distinct();
    }

    public function isSuperAdmin(): bool
    {
        return $this->branchAssignments()
            ->whereHas('role', fn($q) => $q->where('slug', 'super-admin'))
            ->exists();
    }

    public function hasRole(string|array $roleSlugs, ?int $branchId = null): bool
    {
        $slugs = (array) $roleSlugs;
        if ($this->isSuperAdmin()) {
            return true;
        }

        $query = $this->branchAssignments()
            ->whereHas('role', fn($q) => $q->whereIn('slug', $slugs));

        if ($branchId !== null) {
            $query->where(function ($q) use ($branchId) {
                $q->where('branch_id', $branchId)->orWhereNull('branch_id');
            });
        }

        return $query->exists();
    }

    public function hasPermission(string $permissionSlug, ?int $branchId = null): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        $query = $this->branchAssignments();
        if ($branchId !== null) {
            $query->where(function ($q) use ($branchId) {
                $q->where('branch_id', $branchId)->orWhereNull('branch_id');
            });
        }

        $roleIds = $query->pluck('role_id')->unique();
        if ($roleIds->isEmpty()) {
            return false;
        }

        return Permission::where('slug', $permissionSlug)
            ->whereHas('roles', fn($q) => $q->whereIn('roles.id', $roleIds))
            ->exists();
    }

    public function canAccessBranch(int $branchId): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return $this->branchAssignments()
            ->where(function ($q) use ($branchId) {
                $q->where('branch_id', $branchId)->orWhereNull('branch_id');
            })
            ->exists();
    }

    public function getPrimaryBranch(): ?Branch
    {
        $assignment = $this->branchAssignments()
            ->where('is_primary', true)
            ->whereNotNull('branch_id')
            ->with('branch')
            ->first();

        if ($assignment && $assignment->branch) {
            return $assignment->branch;
        }

        $firstBranch = $this->branchAssignments()
            ->whereNotNull('branch_id')
            ->with('branch')
            ->first();

        return $firstBranch ? $firstBranch->branch : Branch::first();
    }

    public function getPrimaryRole(): ?Role
    {
        $primary = $this->branchAssignments()
            ->where('is_primary', true)
            ->with('role')
            ->first();

        if ($primary && $primary->role) {
            return $primary->role;
        }

        $first = $this->branchAssignments()->with('role')->first();
        return $first ? $first->role : null;
    }
}
