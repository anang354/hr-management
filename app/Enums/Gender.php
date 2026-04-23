<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum Gender: string implements HasLabel, HasColor
{
    case Male = 'male';
    case Female = 'female';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Male => __('employee.gender.male'),
            self::Female => __('employee.gender.female'),
        };
    }
    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Male => 'info',    // Warna biru
            self::Female => 'danger', // Warna merah/pink
        };
    }
}
