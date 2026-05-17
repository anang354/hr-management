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
            self::Hadir => __('attendances.status.Hadir'),
            self::Sakit => __('attendances.status.Sakit'),
            self::Izin => __('attendances.status.Izin'),
            self::Cuti => __('attendances.status.Cuti'),
            self::Lembur => __('attendances.status.Lembur'),
            self::Alpha => __('attendances.status.Alpha'),
            self::Libur => __('attendances.status.Libur'),
        };
    }
    // Method untuk mendapatkan singkatan
    public function shortLabel(): string
    {
        return match($this) {
            self::Hadir => 'H',
            self::Sakit => 'S',
            self::Izin  => 'I',
            self::Cuti  => 'C',
            self::Lembur => 'OT',
            self::Alpha => 'A',
            self::Libur => 'Off',
            default     => '?',
        };
    }
    public function getColorClass(): string
    {
        return match($this) {
            self::Hadir  => 'border-green-500',
            self::Alpha  => 'border-red-500',
            self::Libur  => 'border-gray-400',
            self::Lembur => 'border-blue-400',
            self::Sakit, self::Izin, self::Cuti => 'border-orange-400',
            default      => 'border-gray-200',
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
