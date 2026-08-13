<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssetItem extends Model
{
    protected $fillable = [
        'asset_category_id',
        'brand',
        'model',
        'description',
        'photo',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(AssetCategory::class, 'asset_category_id');
    }
    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class, 'asset_item_id');
    }
    public function getAssetCountAttribute()
    {
        return $this->assets()->count();
    }
}
