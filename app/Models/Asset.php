<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Asset extends Model
{
    protected $fillable = [
        'asset_item_id',
        'asset_location_id',
        'asset_code',
        'specifications',
        'status',
        'condition',
        'user',
        'ip_address',
        'date_of_entry',
        'damage_date',
        'notes',
    ];
    protected $casts = [
        'specifications' => 'array',
        'date_of_entry' => 'date',
        'damage_date' => 'date',
        'status' => \App\Enums\AssetStatus::class,
        'condition' => \App\Enums\AssetCondition::class,
    ];
    const STATUS = [
        'active' => 'Active',
        'inactive' => 'Inactive',
        'maintenance' => 'Maintenance',
        'retired' => 'Retired',
    ];
    const CONDITION = [
        'good' => 'Good',
        'damaged' => 'Damaged',
        'needs_repair' => 'Needs Repair',
    ];
    public function item(): BelongsTo
    {
        return $this->belongsTo(AssetItem::class, 'asset_item_id');
    }
    public function location(): BelongsTo
    {
        return $this->belongsTo(AssetLocation::class, 'asset_location_id');
    }
}
