<?php

namespace App\Filament\Resources\RecurringSchedules;

use App\Filament\Resources\RecurringSchedules\Pages\CreateRecurringSchedule;
use App\Filament\Resources\RecurringSchedules\Pages\EditRecurringSchedule;
use App\Filament\Resources\RecurringSchedules\Pages\ListRecurringSchedules;
use App\Filament\Resources\RecurringSchedules\Schemas\RecurringScheduleForm;
use App\Filament\Resources\RecurringSchedules\Tables\RecurringSchedulesTable;
use App\Models\RecurringSchedule;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class RecurringScheduleResource extends Resource
{
    protected static ?string $model = RecurringSchedule::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static ?int $navigationSort = 5;

    public static function getNavigationGroup(): ?string
    {
        return app()->getLocale() === 'ar' ? 'إدارة الشؤون الأكاديمية' : 'Academic Management';
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'الجداول الأسبوعية المتكررة' : 'Recurring Cohort Schedules';
    }

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'جدول أسبوعي متكرر' : 'Recurring Schedule';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'الجداول الأسبوعية المتكررة' : 'Recurring Cohort Schedules';
    }

    public static function form(Schema $schema): Schema
    {
        return RecurringScheduleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RecurringSchedulesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRecurringSchedules::route('/'),
            'create' => CreateRecurringSchedule::route('/create'),
            'edit' => EditRecurringSchedule::route('/{record}/edit'),
        ];
    }
}
