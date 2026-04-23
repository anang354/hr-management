<?php

namespace App\Filament\Resources\OvertimeRequests\Pages;

use App\Filament\Resources\OvertimeRequests\OvertimeRequestResource;
use App\Models\OvertimeRequest;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Icons\Heroicon;

class ListOvertimeRequests extends ListRecords
{
    protected static string $resource = OvertimeRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        // 1. Ambil base query yang sudah berisi logika filter departemen/user dari Resource
        $baseQuery = static::getResource()::getEloquentQuery();

        return [
            'all' => Tab::make('All')
                ->label(__('overtime_request.status.all')),
            'pending' => Tab::make('Pending')
                ->label(__('overtime_request.status.pending'))
                ->badge((clone $baseQuery)->where('status', 'pending')->count())
                ->badgeColor('info')
                // modifyQueryUsing tidak perlu diubah karena ia otomatis melanjutkan query dari getEloquentQuery
                ->modifyQueryUsing(fn($query) => $query->where('status', 'pending')),

            'manager_approved' => Tab::make('Manager Approved')
                ->label(__('overtime_request.status.manager_approved'))
                ->badge((clone $baseQuery)->where('status', 'manager_approved')->count())
                ->badgeColor('primary')
                ->modifyQueryUsing(fn($query) => $query->where('status', 'manager_approved')),

            'approved' => Tab::make('Approved')
                ->label(__('overtime_request.status.approved'))
                ->icon(Heroicon::CheckCircle)
                ->modifyQueryUsing(fn($query) => $query->where('status', 'approved')),

            'rejected' => Tab::make('Rejected')
                ->label(__('overtime_request.status.rejected'))
                ->icon(Heroicon::XCircle)
                ->modifyQueryUsing(fn($query) => $query->where('status', 'rejected')),
        ];
    }

    public function getDefaultActiveTab(): string|int|null
    {
        return 'pending';
    }
}
