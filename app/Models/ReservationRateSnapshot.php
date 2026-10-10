<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReservationRateSnapshot extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_id',
        'stay_date',
        'nightly_rate',
        'tax_amount',
        'discount_amount',
    ];

    protected $casts = [
        'stay_date' => 'date',
        'nightly_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
    ];

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }
}
