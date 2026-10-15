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
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName('Elite Academy')
            ->brandLogo(fn () => request()->isSecure() || app()->environment('production', 'staging') || str_starts_with((string) config('app.url'), 'https://') ? secure_asset('images/logo_500.webp') : asset('images/logo_500.webp'))
            ->brandLogoHeight('2.6rem')
            ->favicon(fn () => request()->isSecure() || app()->environment('production', 'staging') || str_starts_with((string) config('app.url'), 'https://') ? secure_asset('images/logo_500.webp') : asset('images/logo_500.webp'))
            ->darkMode(true)
            ->sidebarCollapsibleOnDesktop()
            ->colors([
                'primary' => Color::Teal,
                'gray' => Color::Slate,
                'amber' => Color::Amber,
                'success' => Color::Emerald,
                'info' => Color::Cyan,
                'danger' => Color::Rose,
                'warning' => Color::Orange,
            ])
            ->renderHook(
                \Filament\View\PanelsRenderHook::HEAD_END,
                fn (): \Illuminate\Contracts\View\View => view('filament.hooks.head-styles')
            )
            ->renderHook(
                \Filament\View\PanelsRenderHook::BODY_END,
                fn (): \Illuminate\Contracts\View\View => view('filament.hooks.ctrl-s-shortcut')
            )
            ->navigationGroups([
                \Filament\Navigation\NavigationGroup::make()
                    ->label(fn () => app()->getLocale() === 'ar' ? 'إدارة الشؤون الأكاديمية' : 'Academic Management')
                    ->icon('heroicon-o-academic-cap'),

                \Filament\Navigation\NavigationGroup::make()
                    ->label(fn () => app()->getLocale() === 'ar' ? 'الجداول والبث المباشر' : 'Schedules & Live Sessions')
                    ->icon('heroicon-o-calendar-days'),

                \Filament\Navigation\NavigationGroup::make()
                    ->label(fn () => app()->getLocale() === 'ar' ? 'إدارة المستخدمين والأدوار' : 'User Management')
                    ->icon('heroicon-o-user-group'),

                \Filament\Navigation\NavigationGroup::make()
                    ->label(fn () => app()->getLocale() === 'ar' ? 'إدارة الباقات والمالية' : 'Packages & Finance')
                    ->icon('heroicon-o-banknotes'),

                \Filament\Navigation\NavigationGroup::make()
                    ->label(fn () => app()->getLocale() === 'ar' ? 'التقارير والإحصائيات' : 'Reports & Analytics')
                    ->icon('heroicon-o-chart-bar'),

                \Filament\Navigation\NavigationGroup::make()
                    ->label(fn () => app()->getLocale() === 'ar' ? 'التواصل والدعم الفني' : 'Communication & Support')
                    ->icon('heroicon-o-chat-bubble-left-right'),

                \Filament\Navigation\NavigationGroup::make()
                    ->label(fn () => app()->getLocale() === 'ar' ? 'إدارة المحتوى والموقع' : 'Landing Page CMS')
                    ->icon('heroicon-o-globe-alt')
                    ->collapsed(),

                \Filament\Navigation\NavigationGroup::make()
                    ->label(fn () => app()->getLocale() === 'ar' ? 'الإعدادات العامة' : 'Settings')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->collapsed(),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
                \App\Filament\Pages\ManageTranslationSystem::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                \App\Filament\Widgets\AdminOverviewStatsWidget::class,
                \App\Filament\Widgets\SessionsAnalyticsChartWidget::class,
                \App\Filament\Widgets\StudentAccountStatusChartWidget::class,
            ])
            ->userMenuItems([
                \Filament\Navigation\MenuItem::make('language_switcher')
                    ->label(fn () => app()->getLocale() === 'ar' ? 'English (🇬🇧 EN)' : 'اللغة العربية (🇪🇬 AR)')
                    ->icon(\Filament\Support\Icons\Heroicon::OutlinedLanguage)
                    ->url(fn () => route('lang.switch', ['locale' => app()->getLocale() === 'ar' ? 'en' : 'ar'])),
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                \App\Http\Middleware\SetLocale::class,
                \App\Http\Middleware\EnsureAdminAccountExists::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
