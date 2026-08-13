<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class AssetCategory extends Model
{
    use HasTranslations;
    protected $fillable = ['name', 'code', 'description'];
    public $translatable = ['name'];

}
