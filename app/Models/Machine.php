<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Machine extends Model
{
    //
    protected $fillable = [
        'name',
        'serial_number',
        'ip_address',
        'port',
        'type',
        'mac_address',
        'location',
        'is_active',
    ];
}
