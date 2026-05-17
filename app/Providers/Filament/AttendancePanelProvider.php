<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Filament\Widgets;

class AttendancePanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('attendance')
            ->login()
            ->path('attendance')
            ->colors([
                'primary' => Color::Purple,
            ])
            ->maxContentWidth('full')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->databaseNotifications()
            ->sidebarCollapsibleOnDesktop()
            ->discoverResources(in: app_path('Filament/Attendance/Resources'), for: 'App\Filament\Attendance\Resources')
            ->discoverPages(in: app_path('Filament/Attendance/Pages'), for: 'App\Filament\Attendance\Pages')
            ->pages([
                Dashboard::class,
                \App\Filament\Resources\Employees\EmployeeResource::class,
                \App\Filament\Resources\EmployeePos\EmployeePosResource::class,
                \App\Filament\Resources\Departments\DepartmentResource::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Attendance/Widgets'), for: 'App\Filament\Attendance\Widgets')
            ->widgets([
                // AccountWidget::class,
                // FilamentInfoWidget::class,
                Widgets\DailyAttendanceChartWidget::class,
                Widgets\DepartmentChartWidget::class,
                Widgets\GenderChart::class,
                Widgets\ReligionChart::class,
                ])
                ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
