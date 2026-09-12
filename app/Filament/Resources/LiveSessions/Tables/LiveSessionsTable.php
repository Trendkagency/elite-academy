<?php

namespace App\Filament\Resources\LiveSessions\Tables;

use App\Models\LiveSession;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LiveSessionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(app()->getLocale() === 'ar' ? 'عنوان الحصة' : 'Session Title')
                    ->weight('bold')
                    ->searchable()
                    ->sortable()
                    ->description(fn (LiveSession $record): string => $record->recurring_schedule_id ? '🔄 ' . ($record->recurringSchedule?->title ?: 'Recurring Cohort') : 'Single Session'),

                TextColumn::make('course.title')
                    ->label(app()->getLocale() === 'ar' ? 'الكورس' : 'Course')
                    ->badge()
                    ->color('primary')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('teacherProfile.user.name')
                    ->label(app()->getLocale() === 'ar' ? 'المعلم' : 'Teacher')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('student.name')
                    ->label(app()->getLocale() === 'ar' ? 'الطالب' : 'Student')
                    ->placeholder(app()->getLocale() === 'ar' ? 'فصل جماعي' : 'Group Cohort')
                    ->searchable(),

                TextColumn::make('scheduled_at')
                    ->label(app()->getLocale() === 'ar' ? 'موعد الحصة' : 'Scheduled At')
                    ->dateTime('Y-m-d h:i A')
                    ->sortable(),

                TextColumn::make('duration_minutes')
                    ->label(app()->getLocale() === 'ar' ? 'المدة' : 'Duration')
                    ->suffix(' ' . (app()->getLocale() === 'ar' ? 'د' : 'min'))
                    ->sortable(),

                TextColumn::make('meeting_platform')
                    ->label(app()->getLocale() === 'ar' ? 'المنصة' : 'Platform')
                    ->badge()
                    ->colors([
                        'primary' => 'agora',
                        'info' => 'zoom',
                        'success' => 'google_meet',
                        'warning' => 'microsoft_teams',
                        'gray' => 'other',
                    ]),

                TextColumn::make('meeting_link')
                    ->label(app()->getLocale() === 'ar' ? 'الرابط' : 'Link')
                    ->copyable()
                    ->copyMessage(app()->getLocale() === 'ar' ? 'تم نسخ الرابط' : 'Meeting link copied')
                    ->limit(25)
                    ->placeholder('—'),

                TextColumn::make('status')
                    ->label(app()->getLocale() === 'ar' ? 'الحالة' : 'Status')
                    ->badge()
                    ->colors([
                        'info' => 'scheduled',
                        'warning' => fn ($state) => in_array($state, ['link_visible', 'rescheduled']),
                        'success' => fn ($state) => in_array($state, ['in_progress', 'completed']),
                        'danger' => fn ($state) => in_array($state, ['cancelled', 'cancelled_by_teacher']),
                    ]),

                IconColumn::make('is_free_demo')
                    ->label(app()->getLocale() === 'ar' ? 'مجانية' : 'Demo')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('is_override')
                    ->label(app()->getLocale() === 'ar' ? 'معدلة' : 'Override')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(app()->getLocale() === 'ar' ? 'تصفية بالحالة' : 'Status Filter')
                    ->options([
                        'scheduled' => 'Scheduled',
                        'link_visible' => 'Link Visible',
                        'in_progress' => 'In Progress',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                        'rescheduled' => 'Rescheduled',
                    ]),

                SelectFilter::make('meeting_platform')
                    ->label(app()->getLocale() === 'ar' ? 'المنصة' : 'Platform Filter')
                    ->options([
                        'agora' => 'Agora Classroom',
                        'google_meet' => 'Google Meet',
                        'zoom' => 'Zoom',
                        'microsoft_teams' => 'Microsoft Teams',
                        'other' => 'Other',
                    ]),

                SelectFilter::make('course_id')
                    ->label(app()->getLocale() === 'ar' ? 'الكورس' : 'Course')
                    ->relationship('course', 'title'),
            ])
            ->recordActions([
                Action::make('updateLink')
                    ->label(app()->getLocale() === 'ar' ? 'تحديث الرابط' : 'Set Link')
                    ->icon('heroicon-o-link')
                    ->color('info')
                    ->modalHeading(app()->getLocale() === 'ar' ? 'تحديث رابط البث المباشر' : 'Update Broadcast Meeting Link')
                    ->form([
                        Select::make('meeting_platform')
                            ->label(app()->getLocale() === 'ar' ? 'المنصة' : 'Platform')
                            ->options([
                                'agora' => 'Agora Classroom',
                                'google_meet' => 'Google Meet',
                                'zoom' => 'Zoom',
                                'microsoft_teams' => 'Microsoft Teams',
                                'other' => 'Other / Custom Stream URL',
                            ])
                            ->required()
                            ->default(fn (LiveSession $record) => $record->meeting_platform ?: 'agora'),

                        TextInput::make('meeting_link')
                            ->label(app()->getLocale() === 'ar' ? 'رابط الاجتماع' : 'Meeting Link')
                            ->url()
                            ->required()
                            ->placeholder('https://zoom.us/j/... or classroom stream link')
                            ->default(fn (LiveSession $record) => $record->meeting_link),
                    ])
                    ->action(function (LiveSession $record, array $data): void {
                        $record->update([
                            'meeting_platform' => $data['meeting_platform'],
                            'meeting_link' => $data['meeting_link'],
                            'status' => 'link_visible',
                            'link_visible_at' => now(),
                        ]);

                        Notification::make()
                            ->title(app()->getLocale() === 'ar' ? 'تم تحديث رابط البث وتفعيله للطلاب بنجاح' : 'Meeting broadcast link updated and published successfully.')
                            ->success()
                            ->send();
                    }),

                Action::make('reschedule')
                    ->label(app()->getLocale() === 'ar' ? 'تأجيل' : 'Reschedule')
                    ->icon('heroicon-o-calendar')
                    ->color('warning')
                    ->modalHeading(app()->getLocale() === 'ar' ? 'تأجيل موعد الحصة' : 'Reschedule Live Session')
                    ->form([
                        DateTimePicker::make('new_scheduled_at')
                            ->label(app()->getLocale() === 'ar' ? 'الموعد الجديد' : 'New Scheduled Date & Time')
                            ->required()
                            ->default(now()->addDay()),

                        TextInput::make('override_reason')
                            ->label(app()->getLocale() === 'ar' ? 'سبب التأجيل' : 'Reason for Rescheduling')
                            ->required(),
                    ])
                    ->action(function (LiveSession $record, array $data): void {
                        $record->update([
                            'original_scheduled_at' => $record->scheduled_at,
                            'scheduled_at' => $data['new_scheduled_at'],
                            'start_at' => $data['new_scheduled_at'],
                            'end_at' => \Illuminate\Support\Carbon::parse($data['new_scheduled_at'])->addMinutes($record->duration_minutes ?: 60),
                            'is_override' => true,
                            'override_reason' => $data['override_reason'],
                            'status' => 'rescheduled',
                        ]);

                        Notification::make()
                            ->title(app()->getLocale() === 'ar' ? 'تم تأجيل الحصة بنجاح وتحديث إشعارات الطلاب' : 'Session rescheduled successfully and notifications queued.')
                            ->warning()
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
            ->defaultSort('scheduled_at', 'desc');
    }
}
