<?php
namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasColor;

enum AssetStatus: string implements HasLabel, HasColor
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Maintenance = 'maintenance';
    case Retired = 'retired';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Active => __('it_portal.asset_status.active'),
            self::Inactive => __('it_portal.asset_status.inactive'),
            self::Maintenance => __('it_portal.asset_status.maintenance'),
            self::Retired => __('it_portal.asset_status.retired'),
        };
    }
    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Active => 'success',
            self::Inactive => 'info',
            self::Maintenance => 'warning',
            self::Retired => 'danger',
        };
    }

}
