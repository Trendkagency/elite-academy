<?php

namespace App\Filament\Resources\LiveSessions\Pages;

use App\Filament\Resources\LiveSessions\LiveSessionResource;
use App\Models\LiveSession;
use App\Services\Analytics\AdminDashboardMetricsService;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;

class ListLiveSessions extends ListRecords
{
    protected static string $resource = LiveSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(app()->getLocale() === 'ar' ? 'إضافة حصة دراسية جديدة' : 'Create Live Session'),
        ];
    }

    public function getTabs(): array
    {
        $metrics = app(AdminDashboardMetricsService::class);

        return [
            'all' => Tab::make(app()->getLocale() === 'ar' ? 'جميع الحصص' : 'All Sessions')
                ->badge(LiveSession::count()),

            'today' => Tab::make(app()->getLocale() === 'ar' ? '📅 حصص اليوم' : '📅 Today')
                ->badge($metrics->sessionsTodayQuery()->count())
                ->modifyQueryUsing(fn ($query) => $metrics->applySessionsToday($query)),

            'live' => Tab::make(app()->getLocale() === 'ar' ? '🔴 جارية الآن / البث المباشر' : '🔴 In Progress / Live')
                ->badge($metrics->sessionsLiveNowQuery()->count())
                ->badgeColor('success')
                ->modifyQueryUsing(fn ($query) => $metrics->applySessionsLiveNow($query)),

            'upcoming' => Tab::make(app()->getLocale() === 'ar' ? '⏳ الحصص القادمة' : '⏳ Upcoming')
                ->badge($metrics->sessionsUpcomingQuery()->count())
                ->modifyQueryUsing(fn ($query) => $metrics->applySessionsUpcoming($query)),

            'free_demo' => Tab::make(app()->getLocale() === 'ar' ? '🎓 تجريبية مجانية' : '🎓 Free Demo')
                ->badge($metrics->sessionsFreeDemoQuery()->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn ($query) => $metrics->applySessionsFreeDemo($query)),

            'regular' => Tab::make(app()->getLocale() === 'ar' ? '📘 حصص المنهج الأساسية' : '📘 Regular Curriculum')
                ->badge($metrics->sessionsRegularQuery()->count())
                ->badgeColor('info')
                ->modifyQueryUsing(fn ($query) => $metrics->applySessionsRegular($query)),

            'completed' => Tab::make(app()->getLocale() === 'ar' ? '✅ المكتملة' : '✅ Completed')
                ->badge($metrics->sessionsCompletedQuery()->count())
                ->modifyQueryUsing(fn ($query) => $metrics->applySessionsCompleted($query)),

            'cancelled' => Tab::make(app()->getLocale() === 'ar' ? '❌ الملغاة' : '❌ Cancelled')
                ->badge($metrics->sessionsCancelledQuery()->count())
                ->badgeColor('danger')
                ->modifyQueryUsing(fn ($query) => $metrics->applySessionsCancelled($query)),
        ];
    }
}
