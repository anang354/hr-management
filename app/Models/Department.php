<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Department extends Model
{
    use HasTranslations;

    protected $fillable = ['name', 'code'];

    public $translatable = ['name'];

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }
    public function getEmployeesActiveAttribute()
    {
        return $this->employees()->where('is_active', true)->count();
    }
}
