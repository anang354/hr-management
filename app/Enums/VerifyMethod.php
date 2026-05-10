<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum VerifyMethod: int implements HasLabel, HasColor, HasIcon
{
    case Finger = 1;
    case Lock = 2;
    case Key = 3;
    case Card = 4;

    public function getLabel(): string
    {
        return match ($this) {
            self::Finger => 'Finger',
            self::Lock => 'Lock',
            self::Key => 'Key',
            self::Card => 'Card',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Finger => 'info',
            self::Lock => 'error',
            self::Key => 'warning',
            self::Card => 'success',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::Finger => 'heroicon-o-finger-print',
            self::Lock => 'heroicon-o-lock-closed',
            self::Key => 'heroicon-o-key',
            self::Card => 'heroicon-o-id-card',
        };
    }
}
