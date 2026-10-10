<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'room_type_id',
        'room_number',
        'floor',
        'operational_status',
        'maintenance_reason',
        'operational_notes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    public function reservationRooms(): HasMany
    {
        return $this->hasMany(ReservationRoom::class);
    }

    public function housekeepingTasks(): HasMany
    {
        return $this->hasMany(HousekeepingTask::class);
    }

    public function maintenanceRequests(): HasMany
    {
        return $this->hasMany(MaintenanceRequest::class);
    }

    public function isAvailableForDates(string $checkIn, string $checkOut, ?int $excludeReservationId = null): bool
    {
        if ($this->operational_status === 'maintenance' || $this->operational_status === 'out_of_order' || !$this->is_active) {
            return false;
        }

        // Check if room has active blocking maintenance
        $hasBlockingMaintenance = $this->maintenanceRequests()
            ->where('blocks_room', true)
            ->whereIn('status', ['open', 'assigned', 'in_progress'])
            ->exists();

        if ($hasBlockingMaintenance) {
            return false;
        }

        // Check overlapping reservations:
        // A reservation overlaps if (existing.check_in < new.check_out AND existing.check_out > new.check_in)
        // and status is in ('confirmed', 'checked_in')
        $query = ReservationRoom::where('room_id', $this->id)
            ->whereHas('reservation', function ($q) use ($checkIn, $checkOut, $excludeReservationId) {
                $q->whereIn('status', ['confirmed', 'checked_in'])
                  ->where('check_in_date', '<', $checkOut)
                  ->where('check_out_date', '>', $checkIn);

                if ($excludeReservationId) {
                    $q->where('id', '!=', $excludeReservationId);
                }
            });

        return !$query->exists();
    }
}
