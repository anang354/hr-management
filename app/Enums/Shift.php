<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum Shift: string implements HasLabel, HasColor, HasIcon
{
  case Day = 'Day';
  case Night = 'Night';

  public function getLabel(): ?string
  {
    return match ($this) {
      self::Day => __('attendances.shift.day'),
      self::Night => __('attendances.shift.night'),
    };
  }
  public function getColor(): string|array|null
  {
    return match ($this) {
      self::Day => 'info',
      self::Night => 'warning',
    };
  }
  public function getIcon(): ?string
  {
    return match ($this) {
      self::Day => 'heroicon-o-sun',
      self::Night => 'heroicon-o-moon',
    };
  }
}
