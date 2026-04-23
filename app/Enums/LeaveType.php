<?php
namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum LeaveType: string implements HasLabel
{
  case Annual = 'Annual';
  case Sick = 'Sick';
  case Personal = 'Personal';
  case Maternity = 'Maternity';
  case Marriage = 'Marriage';

  // Method untuk mendapatkan label multibahasa
  public function getLabel(?string $locale = null): ?string
  {
    return match ($this) {
      self::Annual => __('leave_request.leave_type.annual', [], $locale),
      self::Sick => __('leave_request.leave_type.sick', [], $locale),
      self::Personal => __('leave_request.leave_type.personal', [], $locale),
      self::Maternity => __('leave_request.leave_type.maternity', [], $locale),
      self::Marriage => __('leave_request.leave_type.marriage', [], $locale),
    };
  }
  public function getDualLabel(): string
  {
    return $this->getLabel('en') . ' (' . $this->getLabel('zh_HK') . ')';
  }
}
