<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HotelSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'branch_id',
        'setting_key',
        'setting_value',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public static function get(string $key, $default = null, ?int $branchId = null)
    {
        $query = static::where('setting_key', $key);
        if ($branchId) {
            $branchSetting = (clone $query)->where('branch_id', $branchId)->first();
            if ($branchSetting) {
                return $branchSetting->setting_value;
            }
        }

        $globalSetting = $query->whereNull('branch_id')->first();
        return $globalSetting ? $globalSetting->setting_value : $default;
    }

    public static function set(string $key, $value, ?int $branchId = null, ?int $organizationId = null): void
    {
        static::updateOrCreate(
            [
                'branch_id' => $branchId,
                'setting_key' => $key,
            ],
            [
                'organization_id' => $organizationId,
                'setting_value' => $value,
            ]
        );
    }
}
