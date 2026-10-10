<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'expense_category_id',
        'vendor_id',
        'cashier_shift_id',
        'expense_number',
        'title',
        'description',
        'amount',
        'payment_method',
        'expense_date',
        'status',
        'requested_by_user_id',
        'approved_by_user_id',
        'receipt_path',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(CashierShift::class, 'cashier_shift_id');
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by_user_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(ExpenseApproval::class);
    }

    public static function generateExpenseNumber(int $branchId): string
    {
        $prefix = 'EXP-' . date('Ymd') . '-' . str_pad((string) $branchId, 2, '0', STR_PAD_LEFT);
        $random = strtoupper(substr(bin2hex(random_bytes(3)), 0, 4));
        return "{$prefix}-{$random}";
    }
}
