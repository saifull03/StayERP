<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Refund extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'payment_id',
        'reservation_id',
        'refund_number',
        'amount',
        'reason',
        'status',
        'approved_by_user_id',
        'processed_by_user_id',
        'approved_at',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by_user_id');
    }

    public static function generateRefundNumber(int $branchId): string
    {
        $prefix = 'REF-' . date('Ymd') . '-' . str_pad((string) $branchId, 2, '0', STR_PAD_LEFT);
        $random = strtoupper(substr(bin2hex(random_bytes(3)), 0, 4));
        return "{$prefix}-{$random}";
    }
}
