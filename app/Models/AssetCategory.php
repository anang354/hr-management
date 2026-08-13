<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Spatie\Translatable\HasTranslations;

class AssetCategory extends Model
{
    use HasTranslations;
    protected $fillable = ['name', 'code', 'description'];
    public $translatable = ['name'];
    public function assetItems(): HasMany
    {
        return $this->hasMany(AssetItem::class, 'asset_category_id');
    }
    public function assets(): HasManyThrough
    {
        return $this->hasManyThrough(
            Asset::class,
            AssetItem::class,
            'asset_category_id',
            'asset_item_id',
            'id',
            'id'
        );
    }
    public function totalAssets(): int
    {
        return $this->assets()->count();
    }
}
