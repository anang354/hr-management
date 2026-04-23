<?php
namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Position: string implements HasLabel
{
    case Manager = 'JL';
    case Supervisor = 'ZQ';
    case Leader = 'ZZ';
    case OrdinaryEmployees = 'YC';

    // Method untuk mendapatkan label multibahasa
    public function getLabel(): ?string
    {
        return match ($this) {
            self::Manager => __('positions.manager'),
            self::Supervisor => __('positions.supervisor'),
            self::Leader => __('positions.leader'),
            self::OrdinaryEmployees => __('positions.ordinary_employees'),
        };
    }
}
