<?php

namespace App\Filament\Attendance\Resources\LeaveRequests;

use App\Filament\Attendance\Resources\LeaveRequests\Pages\CreateLeaveRequest;
use App\Filament\Attendance\Resources\LeaveRequests\Pages\EditLeaveRequest;
use App\Filament\Attendance\Resources\LeaveRequests\Pages\ListLeaveRequests;
use App\Filament\Attendance\Resources\LeaveRequests\Schemas\LeaveRequestForm;
use App\Filament\Attendance\Resources\LeaveRequests\Tables\LeaveRequestsTable;
use App\Models\LeaveRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LeaveRequestResource extends Resource
{
    protected static ?string $model = LeaveRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;
    public static function getModelLabel(): string
    {
        return __('leave_request.title');
    }

    public static function getPluralModelLabel(): string
    {
        return __('leave_request.plural_label');
    }

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();
        $query = parent::getEloquentQuery()
            ->orderByDesc('created_at');
        if (in_array($user->role, ['admin', 'hr'])) {
            return $query;
        }
        if ($user->role === 'manager') {
            $myDepartmenId = $user->department?->id;
            return $query->whereHas('user.department', function ($q) use ($myDepartmenId) {
                $q->where('department_id', $myDepartmenId);
            });
        }
        return $query->where('user_id', $user->id);
    }

    public static function getNavigationBadge(): ?string
    {
        $user = auth()->user();
        if (in_array($user->role, ['hr', 'admin'])) {
            return (string) static::getModel()::where('status', 'pending')->count();
        }
        return null;
    }
    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::where('status', 'pending')->count() > 10 ? 'warning' : 'info';
    }
    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return $record->status === 'pending';
    }

    public static function form(Schema $schema): Schema
    {
        return LeaveRequestForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LeaveRequestsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLeaveRequests::route('/'),
            'create' => CreateLeaveRequest::route('/create'),
            'edit' => EditLeaveRequest::route('/{record}/edit'),
        ];
    }
}
