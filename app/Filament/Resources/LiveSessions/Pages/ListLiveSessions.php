<?php

namespace App\Filament\Resources\LiveSessions\Pages;

use App\Filament\Resources\LiveSessions\LiveSessionResource;
use App\Models\LiveSession;
use Filament\Actions\CreateAction;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Resources\Pages\ListRecords;

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
        return [
            'all' => Tab::make(app()->getLocale() === 'ar' ? 'جميع الحصص' : 'All Sessions')
                ->badge(LiveSession::count()),

            'today' => Tab::make(app()->getLocale() === 'ar' ? '📅 حصص اليوم' : '📅 Today')
                ->badge(LiveSession::where(function ($q) {
                    $q->whereDate('scheduled_at', today())->orWhereDate('start_at', today());
                })->count())
                ->modifyQueryUsing(fn ($query) => $query->where(function ($q) {
                    $q->whereDate('scheduled_at', today())->orWhereDate('start_at', today());
                })),

            'live' => Tab::make(app()->getLocale() === 'ar' ? '🔴 جارية الآن / البث المباشر' : '🔴 In Progress / Live')
                ->badge(LiveSession::whereIn('status', ['in_progress', 'live', 'link_visible'])->count())
                ->badgeColor('success')
                ->modifyQueryUsing(fn ($query) => $query->whereIn('status', ['in_progress', 'live', 'link_visible'])),

            'upcoming' => Tab::make(app()->getLocale() === 'ar' ? '⏳ الحصص القادمة' : '⏳ Upcoming')
                ->badge(LiveSession::where(function ($q) {
                    $q->where('scheduled_at', '>', now())->orWhere('start_at', '>', now());
                })->whereNotIn('status', ['completed', 'cancelled', 'cancelled_by_teacher'])->count())
                ->modifyQueryUsing(fn ($query) => $query->where(function ($q) {
                    $q->where('scheduled_at', '>', now())->orWhere('start_at', '>', now());
                })->whereNotIn('status', ['completed', 'cancelled', 'cancelled_by_teacher'])),

            'free_demo' => Tab::make(app()->getLocale() === 'ar' ? '🎓 تجريبية مجانية' : '🎓 Free Demo')
                ->badge(LiveSession::where('is_free_demo', true)->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn ($query) => $query->where('is_free_demo', true)),

            'completed' => Tab::make(app()->getLocale() === 'ar' ? '✅ المكتملة' : '✅ Completed')
                ->badge(LiveSession::where('status', 'completed')->count())
                ->modifyQueryUsing(fn ($query) => $query->where('status', 'completed')),

            'cancelled' => Tab::make(app()->getLocale() === 'ar' ? '❌ الملغاة' : '❌ Cancelled')
                ->badge(LiveSession::whereIn('status', ['cancelled', 'cancelled_by_teacher'])->count())
                ->badgeColor('danger')
                ->modifyQueryUsing(fn ($query) => $query->whereIn('status', ['cancelled', 'cancelled_by_teacher'])),
        ];
    }
}
