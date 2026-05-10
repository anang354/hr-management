<?php

namespace App\Filament\Attendance\Resources\OvertimeRequests;

use App\Filament\Attendance\Resources\OvertimeRequests\Pages\CreateOvertimeRequest;
use App\Filament\Attendance\Resources\OvertimeRequests\Pages\EditOvertimeRequest;
use App\Filament\Attendance\Resources\OvertimeRequests\Pages\ListOvertimeRequests;
use App\Filament\Attendance\Resources\OvertimeRequests\Schemas\OvertimeRequestForm;
use App\Filament\Attendance\Resources\OvertimeRequests\Tables\OvertimeRequestsTable;
use App\Models\OvertimeRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OvertimeRequestResource extends Resource
{
    protected static ?string $model = OvertimeRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ClipboardDocumentCheck;
    public static function getNavigationGroup(): ?string
    {
        return __('overtime_request.navigation_group') ?? null;
    }

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();
        $query = parent::getEloquentQuery()
            ->withCount([
                'items as total_items_count',
                'items as approved_items_count' => fn($query) => $query->where('status', \App\Enums\OvertimeItemStatus::Approved),

                'items as rejected_items_count' => fn($query) => $query->where('status', \App\Enums\OvertimeItemStatus::Rejected),
            ])
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

        // 1. Jika HR atau Admin: Hitung semua yang berstatus 'manager_approved'
        if (in_array($user->role, ['hr', 'admin'])) {
            return (string) static::getModel()::where('status', 'manager_approved')->count();
        }

        // 2. Jika Manager: Hitung yang 'pending' di departemennya sendiri
        if ($user->role === 'manager') {
            return (string) static::getModel()::where('status', 'pending')
                ->whereHas('user', fn($query) => $query->where('department_id', $user->department_id))
                ->count();
        }

        // Role lain (seperti Leader) tidak menampilkan badge
        return null;
    }
    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function getModelLabel(): string
    {
        return __('overtime_request.title');
    }

    public static function getPluralModelLabel(): string
    {
        return __('overtime_request.plural_label');
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        $user = auth()->user();

        // Jika user adalah Admin atau HR, mereka tetap bisa edit kapan saja (opsional)
        if (in_array($user->role, ['admin', 'hr'])) {
            return true;
        }

        // Jika user adalah Leader/Manager, hanya bisa edit jika status masih PENDING
        // Ganti 'pending' dengan value/enum yang Anda gunakan
        return $record->status === \App\Enums\OvertimeStatus::Pending;
    }
    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        $user = auth()->user();

        // Jika user adalah Admin atau HR, mereka tetap bisa edit kapan saja (opsional)
        if (in_array($user->role, ['admin', 'hr'])) {
            return true;
        }

        // Jika user adalah Leader/Manager, hanya bisa edit jika status masih PENDING
        // Ganti 'pending' dengan value/enum yang Anda gunakan
        return $record->status === \App\Enums\OvertimeStatus::Pending;
    }

    public static function form(Schema $schema): Schema
    {
        return OvertimeRequestForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OvertimeRequestsTable::configure($table);
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
            'index' => ListOvertimeRequests::route('/'),
            'create' => CreateOvertimeRequest::route('/create'),
            'detail' => Pages\DetailOvertime::route('/{record}/detail'),
            'edit' => EditOvertimeRequest::route('/{record}/edit'),
        ];
    }
}
