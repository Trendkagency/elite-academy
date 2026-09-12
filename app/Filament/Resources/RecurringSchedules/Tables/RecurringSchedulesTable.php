<?php

namespace App\Filament\Resources\RecurringSchedules\Tables;

use App\Models\RecurringSchedule;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class RecurringSchedulesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(app()->getLocale() === 'ar' ? 'عنوان الجدول' : 'Schedule Title')
                    ->weight('bold')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('course.title')
                    ->label(app()->getLocale() === 'ar' ? 'الكورس' : 'Course')
                    ->badge()
                    ->color('primary')
                    ->searchable(),

                TextColumn::make('teacherProfile.user.name')
                    ->label(app()->getLocale() === 'ar' ? 'المعلم' : 'Teacher')
                    ->searchable(),

                TextColumn::make('studentUser.name')
                    ->label(app()->getLocale() === 'ar' ? 'الطالب' : 'Student')
                    ->placeholder(app()->getLocale() === 'ar' ? 'دفعة عامة' : 'Public Cohort')
                    ->searchable(),

                TextColumn::make('recurrence_type')
                    ->label(app()->getLocale() === 'ar' ? 'النمط' : 'Pattern')
                    ->badge()
                    ->color('info'),

                TextColumn::make('start_time')
                    ->label(app()->getLocale() === 'ar' ? 'التوقيت والمدة' : 'Time & Duration')
                    ->formatStateUsing(fn (RecurringSchedule $record): string => $record->start_time . ' (' . $record->duration_minutes . ' min)'),

                TextColumn::make('start_date')
                    ->label(app()->getLocale() === 'ar' ? 'الفترة الزمنية' : 'Date Range')
                    ->formatStateUsing(fn (RecurringSchedule $record): string => ($record->start_date?->format('Y-m-d') ?: '—') . ' → ' . ($record->end_date?->format('Y-m-d') ?: '—')),

                TextColumn::make('live_sessions_count')
                    ->label(app()->getLocale() === 'ar' ? 'الحصص المتولدة' : 'Generated Sessions')
                    ->counts('liveSessions')
                    ->badge()
                    ->color('success'),

                TextColumn::make('status')
                    ->label(app()->getLocale() === 'ar' ? 'الحالة' : 'Status')
                    ->badge()
                    ->colors([
                        'success' => 'active',
                        'warning' => 'paused',
                        'gray' => 'completed',
                        'danger' => 'cancelled',
                    ]),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(app()->getLocale() === 'ar' ? 'الحالة' : 'Status')
                    ->options([
                        'active' => 'Active',
                        'paused' => 'Paused',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),

                SelectFilter::make('course_id')
                    ->label(app()->getLocale() === 'ar' ? 'الكورس' : 'Course')
                    ->relationship('course', 'title'),
            ])
            ->recordActions([
                Action::make('togglePause')
                    ->label(fn (RecurringSchedule $record) => $record->status === 'active' 
                        ? (app()->getLocale() === 'ar' ? 'إيقاف مؤقت' : 'Pause') 
                        : (app()->getLocale() === 'ar' ? 'تفعيل' : 'Activate'))
                    ->icon(fn (RecurringSchedule $record) => $record->status === 'active' ? 'heroicon-o-pause' : 'heroicon-o-play')
                    ->color(fn (RecurringSchedule $record) => $record->status === 'active' ? 'warning' : 'success')
                    ->requiresConfirmation()
                    ->action(function (RecurringSchedule $record): void {
                        $newStatus = $record->status === 'active' ? 'paused' : 'active';
                        $record->update(['status' => $newStatus]);

                        Notification::make()
                            ->title(app()->getLocale() === 'ar' ? "تم تغيير حالة الجدول إلى {$newStatus}" : "Schedule status updated to {$newStatus}")
                            ->success()
                            ->send();
                    }),

                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
