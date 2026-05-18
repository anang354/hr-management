<?php

namespace App\Providers;

use BezhanSalleh\LanguageSwitch\LanguageSwitch;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
    //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(['en', 'zh_HK']) // Tentukan bahasa yang tersedia
                ->labels([
                'en' => 'English',
                'zh_HK' => '简体中文'
            ]);
        });
        FilamentView::registerRenderHook(
            PanelsRenderHook::SIDEBAR_NAV_START,
            function (): string {
                // Proteksi null-safe (?->) jika sewaktu-waktu diakses saat user belum login
                if (auth()->user()->role === 'admin' || auth()->user()->role === 'hr_all') {
                    return Blade::render('<livewire:action-shortcuts />');
                }

                return '';
        },
        );
    }
}
