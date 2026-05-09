<?php
namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasColor;

enum AttendanceType: string implements HasLabel, HasColor
{
    case Masuk = '0';
    case Pulang = '1';
    case Keluar = '2';
    case Kembali = '3';
    case Lembur_Masuk = '4';
    case Lembur_Pulang = '5';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Masuk => 'Masuk',
            self::Pulang => 'Pulang',
            self::Keluar => 'Keluar',
            self::Kembali => 'Kembali',
            self::Lembur_Masuk => 'Lembur Masuk',
            self::Lembur_Pulang => 'Lembur Pulang',
        };
    }
    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Masuk => 'success',
            self::Pulang => 'warning',
            self::Keluar => 'danger',
            self::Kembali => 'info',
            self::Lembur_Masuk => 'primary',
            self::Lembur_Pulang => 'secondary',
        };
    }
}
