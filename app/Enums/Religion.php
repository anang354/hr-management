<?php
namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Religion: string implements HasLabel
{
    case Islam = 'Islam';
    case Christian = 'Kristen';
    case Catholic = 'Katholik';
    case Hindu = 'Hindu';
    case Buddha = 'Buddha';

    // Method untuk mendapatkan label multibahasa
    public function getLabel(): ?string
    {
        return match ($this) {
            self::Islam => __('employee.religion.islam'),
            self::Christian => __('employee.religion.christian'),
            self::Catholic => __('employee.religion.catholic'),
            self::Hindu => __('employee.religion.hindu'),
            self::Buddha => __('employee.religion.buddha'),
        };
    }
}
