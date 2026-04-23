<?php
namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Education: string implements HasLabel
{
    case HighSchool = 'SMA Sederajat';
    case Diploma = 'D3';
    case Bachelor = 'S1';
    case Master = 'S2';
    case Doctor = 'S3';

    // Method untuk mendapatkan label multibahasa
    public function getLabel(): ?string
    {
        return match ($this) {
            self::HighSchool => __('employee.education.high_school'),
            self::Diploma => __('employee.education.diploma'),
            self::Bachelor => __('employee.education.bachelor'),
            self::Master => __('employee.education.master'),
            self::Doctor => __('employee.education.doctor'),
        };
    }
}
