<?php
namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasColor;

enum AttendanceStatus: string implements HasLabel, HasColor
{
    case Hadir = 'Hadir';
    case Sakit = 'Sakit';
    case Izin = 'Izin';
    case Cuti = 'Cuti';
    case Lembur = 'Lembur';
    case Alpha = 'Alpha';
    case Libur = 'Libur';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Hadir => 'Hadir',
            self::Sakit => 'Sakit',
            self::Izin => 'Izin',
            self::Cuti => 'Cuti',
            self::Lembur => 'Lembur',
            self::Alpha => 'Alpha',
            self::Libur => 'Libur',
        };
    }
    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Hadir => 'success',
            self::Sakit => 'warning',
            self::Izin => 'info',
            self::Cuti => 'info',
            self::Lembur => 'primary',
            self::Alpha => 'danger',
            self::Libur => 'secondary',
        };
    }
}
