<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'reservation_id',
        'invoice_number',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'grand_total',
        'paid_amount',
        'balance_due',
        'status',
        'issued_date',
        'due_date',
        'notes',
        'created_by_user_id',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'balance_due' => 'decimal:2',
        'issued_date' => 'date',
        'due_date' => 'date',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public static function generateInvoiceNumber(int $branchId): string
    {
        $prefix = 'INV-' . date('Ymd') . '-' . str_pad((string) $branchId, 2, '0', STR_PAD_LEFT);
        $random = strtoupper(substr(bin2hex(random_bytes(3)), 0, 4));
        return "{$prefix}-{$random}";
    }

    public function recalculateTotals(): void
    {
        $subtotal = round((float) $this->items()->sum('total_amount'), 2);
        $grandTotal = round(max(0, $subtotal + (float) $this->tax_amount - (float) $this->discount_amount), 2);
        $paid = round((float) $this->payments()->where('status', 'completed')->sum('amount'), 2);
        $balance = round(max(0, $grandTotal - $paid), 2);

        $status = 'unpaid';
        if ($balance <= 0 && $grandTotal > 0) {
            $status = 'paid';
        } elseif ($paid > 0 && $balance > 0) {
            $status = 'partially_paid';
        }

        $this->update([
            'subtotal' => $subtotal,
            'grand_total' => $grandTotal,
            'paid_amount' => $paid,
            'balance_due' => $balance,
            'status' => $status,
        ]);
    }
}
