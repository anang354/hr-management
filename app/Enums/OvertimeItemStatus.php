<?php
namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum OvertimeItemStatus: string implements HasLabel, HasColor, HasIcon
{
  case Pending = 'pending';
  case Approved = 'approved';
  case Rejected = 'rejected';

  public function getLabel(): string
  {
    return match ($this) {
      self::Pending => __('overtime_request.status.pending'),
      self::Approved => __('overtime_request.status.approved'),
      self::Rejected => __('overtime_request.status.rejected'),
    };
  }

  public function getColor(): string
  {
    return match ($this) {
      self::Pending => 'warning',
      self::Approved => 'success',
      self::Rejected => 'danger',
    };
  }

  public function getIcon(): string
  {
    return match ($this) {
      self::Pending => 'heroicon-o-clock',
      self::Approved => 'heroicon-o-check-circle',
      self::Rejected => 'heroicon-o-x-circle',
    };
  }
}
