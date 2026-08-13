<?php
namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum AssetCondition: string implements HasLabel, HasColor, HasIcon
{
    case Good = 'good';
    case Damaged = 'damaged';
    case Repair = 'needs_repair';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Good => __('it_portal.asset_condition.good'),
            self::Damaged => __('it_portal.asset_condition.damaged'),
            self::Repair => __('it_portal.asset_condition.needs_repair'),
        };
    }
    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Good => 'success',
            self::Damaged => 'danger',
            self::Repair => 'warning',
        };
    }
    public function getIcon(): string
    {
        return match ($this) {
            self::Good => 'heroicon-o-check-badge',
            self::Damaged => 'heroicon-o-x-circle',
            self::Repair => 'heroicon-o-wrench-screwdriver',
        };
    }
}
