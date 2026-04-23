<?php

namespace App\Filament\Resources\LeaveRequests\Pages;

use App\Filament\Resources\LeaveRequests\LeaveRequestResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Contracts\Database\Eloquent\Builder;
class ListLeaveRequests extends ListRecords
{
    protected static string $resource = LeaveRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'pending' => Tab::make(__('leave_request.status.pending'))
                ->icon('heroicon-o-clock')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'pending')),
            'approved' => Tab::make(__('leave_request.status.approved'))
                ->icon('heroicon-o-check-badge')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'approved')),
            'rejected' => Tab::make(__('leave_request.status.rejected'))
                ->icon('heroicon-o-x-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'rejected')),
        ];
    }
    public function getDefaultActiveTab(): string|int|null
    {
        return 'pending';
    }
}
