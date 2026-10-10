<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CashierShift extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'user_id',
        'shift_number',
        'opening_cash',
        'closing_cash_counted',
        'expected_closing_cash',
        'cash_discrepancy',
        'discrepancy_reason',
        'status',
        'opened_at',
        'closed_at',
        'reviewed_by_user_id',
        'reviewed_at',
        'notes',
    ];

    protected $casts = [
        'opening_cash' => 'decimal:2',
        'closing_cash_counted' => 'decimal:2',
        'expected_closing_cash' => 'decimal:2',
        'cash_discrepancy' => 'decimal:2',
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by_user_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(CashierTransaction::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public static function generateShiftNumber(int $branchId): string
    {
        $prefix = 'SHF-' . date('Ymd') . '-' . str_pad((string) $branchId, 2, '0', STR_PAD_LEFT);
        $random = strtoupper(substr(bin2hex(random_bytes(3)), 0, 4));
        return "{$prefix}-{$random}";
    }

    public function getCashCollectedAttribute(): float
    {
        return round((float) $this->payments()
            ->where('payment_method', 'cash')
            ->where('status', 'completed')
            ->sum('amount'), 2);
    }

    public function getNonCashCollectedAttribute(): float
    {
        return round((float) $this->payments()
            ->where('payment_method', '!=', 'cash')
            ->where('status', 'completed')
            ->sum('amount'), 2);
    }

    public function getCashExpensesAttribute(): float
    {
        return round((float) $this->expenses()
            ->where('payment_method', 'cash')
            ->whereIn('status', ['approved', 'paid'])
            ->sum('amount'), 2);
    }

    public function calculateExpectedCash(): float
    {
        $opening = (float) $this->opening_cash;
        $cashIn = $this->cash_collected;
        $cashExpenses = $this->cash_expenses;

        // Cash refunds processed during this shift
        $cashRefunds = (float) CashierTransaction::where('cashier_shift_id', $this->id)
            ->where('transaction_type', 'refund_paid')
            ->sum('amount');

        return round($opening + $cashIn - $cashExpenses - $cashRefunds, 2);
    }
}
