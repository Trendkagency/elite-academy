<?php

namespace App\Providers\Filament;

use App\Filament\Pages\ManageTranslationSystem;
use App\Filament\Widgets\AdminOverviewStatsWidget;
use App\Filament\Widgets\SessionsAnalyticsChartWidget;
use App\Filament\Widgets\StudentAccountStatusChartWidget;
use App\Http\Middleware\EnsureAdminAccountExists;
use App\Http\Middleware\SetLocale;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\FontProviders\GoogleFontProvider;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationGroup;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\View\PanelsRenderHook;
use Illuminate\Contracts\View\View;
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
            ->brandName(fn () => brand('app_name', 'Elite Academy'))
            ->brandLogo(fn () => brand_logo('light', 'images/logo_500.webp'))
            ->darkModeBrandLogo(fn () => brand_logo('dark', 'images/logo_500.webp'))
            ->brandLogoHeight('2.6rem')
            ->favicon(fn () => brand_logo('light', 'images/logo_500.webp'))
            ->maxContentWidth(Width::Full)
            ->font(
                family: fn (): string => app()->getLocale() === 'ar' ? 'Cairo' : 'Inter',
                provider: GoogleFontProvider::class,
            )
            ->plugins([
                FilamentShieldPlugin::make(),
            ])
            ->darkMode(true)
            ->sidebarCollapsibleOnDesktop()
            ->colors([
                'primary' => demo_enabled() && config('demo.branding.primary_color') ? Color::hex(config('demo.branding.primary_color')) : Color::Teal,
                'gray' => Color::Slate,
                'amber' => Color::Amber,
                'success' => Color::Emerald,
                'info' => Color::Cyan,
                'danger' => Color::Rose,
                'warning' => Color::Orange,
            ])
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): View => view('filament.hooks.head-styles')
            )
            ->renderHook(
                PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE,
                fn (): View => view('filament.hooks.demo-login-card')
            )
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn (): View => view('filament.hooks.ctrl-s-shortcut')
            )
            ->navigationGroups([
                NavigationGroup::make()
                    ->label(fn () => app()->getLocale() === 'ar' ? 'إدارة الشؤون الأكاديمية' : 'Academic Management')
                    ->icon('heroicon-o-academic-cap'),

                NavigationGroup::make()
                    ->label(fn () => app()->getLocale() === 'ar' ? 'الجداول والبث المباشر' : 'Schedules & Live Sessions')
                    ->icon('heroicon-o-calendar-days'),

                NavigationGroup::make()
                    ->label(fn () => app()->getLocale() === 'ar' ? 'إدارة المستخدمين والأدوار' : 'User Management')
                    ->icon('heroicon-o-user-group'),

                NavigationGroup::make()
                    ->label(fn () => app()->getLocale() === 'ar' ? 'إدارة الباقات والمالية' : 'Packages & Finance')
                    ->icon('heroicon-o-banknotes'),

                NavigationGroup::make()
                    ->label(fn () => app()->getLocale() === 'ar' ? 'التقارير والإحصائيات' : 'Reports & Analytics')
                    ->icon('heroicon-o-chart-bar'),

                NavigationGroup::make()
                    ->label(fn () => app()->getLocale() === 'ar' ? 'التواصل والدعم الفني' : 'Communication & Support')
                    ->icon('heroicon-o-chat-bubble-left-right'),

                NavigationGroup::make()
                    ->label(fn () => app()->getLocale() === 'ar' ? 'إدارة المحتوى والموقع' : 'Landing Page CMS')
                    ->icon('heroicon-o-globe-alt')
                    ->collapsed(),

                NavigationGroup::make()
                    ->label(fn () => app()->getLocale() === 'ar' ? 'الإعدادات العامة' : 'Settings')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->collapsed(),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
                ManageTranslationSystem::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AdminOverviewStatsWidget::class,
                SessionsAnalyticsChartWidget::class,
                StudentAccountStatusChartWidget::class,
            ])
            ->userMenuItems([
                MenuItem::make('language_switcher')
                    ->label(fn () => app()->getLocale() === 'ar' ? 'English (🇬🇧 EN)' : 'اللغة العربية (🇪🇬 AR)')
                    ->icon(Heroicon::OutlinedLanguage)
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
                SetLocale::class,
                EnsureAdminAccountExists::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
