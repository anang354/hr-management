<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContractSetting extends Model
{
    //
    protected $fillable = [
        'hr_name',
        'hr_position',
        'contract_template',
        'sign_1',
        'position_1',
        'sign_2',
        'position_2',
    ];
}
