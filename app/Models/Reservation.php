<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'guest_id',
        'reservation_number',
        'check_in_date',
        'check_out_date',
        'status',
        'source',
        'adults',
        'children',
        'special_requests',
        'cancellation_reason',
        'checked_in_at',
        'checked_out_at',
        'cancelled_at',
        'created_by_user_id',
    ];

    protected $casts = [
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'checked_in_at' => 'datetime',
        'checked_out_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'adults' => 'integer',
        'children' => 'integer',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function reservationRooms(): HasMany
    {
        return $this->hasMany(ReservationRoom::class);
    }

    public function rateSnapshots(): HasMany
    {
        return $this->hasMany(ReservationRateSnapshot::class);
    }

    public function folioItems(): HasMany
    {
        return $this->hasMany(GuestFolioItem::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function latestInvoice(): HasOne
    {
        return $this->hasOne(Invoice::class)->latestOfMany();
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(Refund::class);
    }

    public function getNightsCountAttribute(): int
    {
        $checkIn = Carbon::parse($this->check_in_date);
        $checkOut = Carbon::parse($this->check_out_date);
        $nights = $checkIn->diffInDays($checkOut);
        return $nights > 0 ? (int) $nights : 1;
    }

    public function getTotalChargesAttribute(): float
    {
        $roomCharges = (float) $this->reservationRooms()->sum('total_amount');
        $folioCharges = (float) $this->folioItems()->sum('total_amount');
        return round($roomCharges + $folioCharges, 2);
    }

    public function getTotalPaidAttribute(): float
    {
        return round((float) $this->payments()->where('status', 'completed')->sum('amount'), 2);
    }

    public function getBalanceDueAttribute(): float
    {
        return round(max(0, $this->total_charges - $this->total_paid), 2);
    }

    public static function generateReservationNumber(int $branchId): string
    {
        $prefix = 'RES-' . date('Ymd') . '-' . str_pad((string) $branchId, 2, '0', STR_PAD_LEFT);
        $random = strtoupper(substr(bin2hex(random_bytes(3)), 0, 4));
        return "{$prefix}-{$random}";
    }
}
