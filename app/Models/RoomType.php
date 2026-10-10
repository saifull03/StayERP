<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RoomType extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'name',
        'slug',
        'description',
        'base_price_per_night',
        'max_adults',
        'max_children',
        'total_capacity',
        'size_sqft',
        'is_active',
    ];

    protected $casts = [
        'base_price_per_night' => 'decimal:2',
        'is_active' => 'boolean',
        'max_adults' => 'integer',
        'max_children' => 'integer',
        'total_capacity' => 'integer',
        'size_sqft' => 'integer',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(RoomAmenity::class, 'room_type_amenities');
    }
}
