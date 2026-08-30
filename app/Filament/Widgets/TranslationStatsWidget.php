<?php

namespace App\Filament\Widgets;

use App\Models\TranslationKey;
use App\Models\TranslationValue;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TranslationStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        if (!\Illuminate\Support\Facades\Schema::hasTable('translation_keys') || !\Illuminate\Support\Facades\Schema::hasTable('translation_values')) {
            return [
                Stat::make(__('Translation System'), __('Pending Migration'))
                    ->description(__('Run: php artisan migrate --seed'))
                    ->icon('heroicon-o-exclamation-triangle')
                    ->color('warning'),
            ];
        }

        $totalKeys = TranslationKey::count();
        $enCount = TranslationValue::where('locale', 'en')->whereNotNull('value')->where('value', '!=', '')->count();
        $arCount = TranslationValue::where('locale', 'ar')->whereNotNull('value')->where('value', '!=', '')->count();

        $missingCount = TranslationKey::whereDoesntHave('values', function ($q) {
            $q->where('locale', 'ar')->whereNotNull('value')->where('value', '!=', '');
        })->orWhereDoesntHave('values', function ($q) {
            $q->where('locale', 'en')->whereNotNull('value')->where('value', '!=', '');
        })->count();

        $lockedCount = TranslationValue::where('is_locked', true)->count();

        return [
            Stat::make(__('Total Keys'), number_format($totalKeys))
                ->description(__('Registered translation keys'))
                ->icon('heroicon-o-key')
                ->color('info'),

            Stat::make(__('Arabic Complete'), number_format($arCount) . " / {$totalKeys}")
                ->description(__('Localized in Arabic (ar)'))
                ->icon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make(__('English Complete'), number_format($enCount) . " / {$totalKeys}")
                ->description(__('Localized in English (en)'))
                ->icon('heroicon-o-check-badge')
                ->color('primary'),

            Stat::make(__('Missing Translations'), number_format($missingCount))
                ->description(__('Keys with missing locale string'))
                ->icon('heroicon-o-exclamation-triangle')
                ->color($missingCount > 0 ? 'danger' : 'success'),

            Stat::make(__('Locked / Reviewed'), number_format($lockedCount))
                ->description(__('Protected from bulk overwrite'))
                ->icon('heroicon-o-lock-closed')
                ->color('warning'),
        ];
    }
}
