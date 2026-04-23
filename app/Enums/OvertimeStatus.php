<?php
namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum OvertimeStatus: string implements HasLabel, HasColor
{
    case Pending = 'pending';
    case ManagerApproved = 'manager_approved';
    case Approved = 'approved';
    case Rejected = 'rejected';

    public function getLabel(): string
    {
        return match ($this) {
            self::Pending => __('overtime_request.status.pending'),
            self::ManagerApproved => __('overtime_request.status.manager_approved'),
            self::Approved => __('overtime_request.status.approved'),
            self::Rejected => __('overtime_request.status.rejected'),
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Pending => 'gray',
            self::ManagerApproved => 'info',
            self::Approved => 'success',
            self::Rejected => 'danger',
        };
    }
}
