<?php

namespace App\Filament\Resources\Courses\RelationManagers;

use App\Models\CourseEnrollment;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class EnrollmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'enrollments';

    protected static string|BackedEnum|null $icon = 'heroicon-o-users';

    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): string
    {
        return __('Enrolled Students & Cohorts');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('student_user_id')
                    ->relationship(
                        name: 'studentUser',
                        titleAttribute: 'name',
                        modifyQueryUsing: function ($query) {
                            if (method_exists(User::class, 'scopeRoleStudent')) {
                                return $query->roleStudent()->orWhereHas('studentProfile')->latest('id');
                            }
                            return $query->whereHas('studentProfile')->latest('id');
                        }
                    )
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} (#{$record->studentProfile?->student_code}) - {$record->email}")
                    ->label(__('Student'))
                    ->searchable(['name', 'email'])
                    ->preload()
                    ->required(),

                TextInput::make('cohort')
                    ->label(__('Cohort / Class Group'))
                    ->placeholder(__('e.g. Group A, Fall 2026, Morning Cohort'))
                    ->maxLength(100)
                    ->nullable(),

                Select::make('status')
                    ->label(__('Enrollment Status'))
                    ->options([
                        'active' => __('Active'),
                        'completed' => __('Completed'),
                        'dropped' => __('Dropped'),
                    ])
                    ->default('active')
                    ->required(),

                TextInput::make('progress_percent')
                    ->label(__('Progress Percentage %'))
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100)
                    ->default(0)
                    ->required(),

                DateTimePicker::make('enrolled_at')
                    ->label(__('Enrollment Date'))
                    ->default(now())
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('studentUser.name')
            ->columns([
                TextColumn::make('studentUser.name')
                    ->label(__('Student Name'))
                    ->description(fn (CourseEnrollment $record): string => $record->studentUser?->email ?? '')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('studentUser.studentProfile.student_code')
                    ->label(__('Student Code'))
                    ->badge()
                    ->color('gray')
                    ->searchable(),

                TextColumn::make('studentUser.studentProfile.gradeLevel.name')
                    ->label(__('Grade'))
                    ->default('—')
                    ->sortable(),

                TextColumn::make('cohort')
                    ->label(__('Cohort'))
                    ->badge()
                    ->color('primary')
                    ->default(__('General Cohort'))
                    ->searchable(),

                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => __('Active'),
                        'completed' => __('Completed'),
                        'dropped' => __('Dropped'),
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'completed' => 'info',
                        'dropped' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('progress_percent')
                    ->label(__('Progress'))
                    ->suffix('%')
                    ->sortable(),

                TextColumn::make('enrolled_at')
                    ->label(__('Enrolled At'))
                    ->dateTime('d M Y, H:i')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label(__('Updated At'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([
                Action::make('enroll_multiple_students')
                    ->label(__('Enroll Multiple Students'))
                    ->icon('heroicon-o-user-plus')
                    ->color('success')
                    ->modalHeading(__('Enroll Multiple Students in Course'))
                    ->modalDescription(__('Select multiple students to instantly enroll them into this course without conflict.'))
                    ->modalSubmitActionLabel(__('Enroll Selected Students'))
                    ->schema([
                        Select::make('student_user_ids')
                            ->label(__('Select Students'))
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->options(function () {
                                $query = User::query();
                                if (method_exists(User::class, 'scopeRoleStudent')) {
                                    $query->roleStudent()->orWhereHas('studentProfile');
                                } else {
                                    $query->whereHas('studentProfile');
                                }

                                return $query
                                    ->with('studentProfile.gradeLevel')
                                    ->orderBy('name')
                                    ->get()
                                    ->mapWithKeys(function (User $user) {
                                        $code = $user->studentProfile?->student_code ? "#{$user->studentProfile->student_code}" : '';
                                        $grade = $user->studentProfile?->gradeLevel?->name ? "({$user->studentProfile->gradeLevel->name})" : '';
                                        $label = trim("{$user->name} {$code} {$grade} - {$user->email}");

                                        return [$user->id => $label];
                                    });
                            })
                            ->helperText(__('You can search by name, code or email and select as many students as needed.'))
                            ->required(),

                        TextInput::make('cohort')
                            ->label(__('Cohort / Group Name'))
                            ->placeholder(__('e.g. Group A, Fall 2026, Secondary 3 Group'))
                            ->nullable(),

                        Select::make('status')
                            ->label(__('Enrollment Status'))
                            ->options([
                                'active' => __('Active'),
                                'completed' => __('Completed'),
                                'dropped' => __('Dropped'),
                            ])
                            ->default('active')
                            ->required(),

                        DateTimePicker::make('enrolled_at')
                            ->label(__('Enrollment Date'))
                            ->default(now())
                            ->required(),
                    ])
                    ->action(function (array $data, RelationManager $livewire): void {
                        $course = $livewire->getOwnerRecord();
                        $studentIds = (array) ($data['student_user_ids'] ?? []);
                        $cohort = ! empty($data['cohort']) ? trim($data['cohort']) : null;
                        $status = $data['status'] ?? 'active';
                        $enrolledAt = $data['enrolled_at'] ?? now();

                        $enrolledCount = 0;
                        $updatedCount = 0;

                        foreach ($studentIds as $studentId) {
                            $existing = CourseEnrollment::where('course_id', $course->id)
                                ->where('student_user_id', $studentId)
                                ->first();

                            if ($existing) {
                                $existing->update([
                                    'cohort' => $cohort ?? $existing->cohort,
                                    'status' => $status,
                                ]);
                                $updatedCount++;
                            } else {
                                CourseEnrollment::create([
                                    'course_id' => $course->id,
                                    'student_user_id' => $studentId,
                                    'cohort' => $cohort,
                                    'status' => $status,
                                    'progress_percent' => 0,
                                    'enrolled_at' => $enrolledAt,
                                ]);
                                $enrolledCount++;
                            }
                        }

                        Notification::make()
                            ->title(__('Students Enrolled Successfully'))
                            ->body(__("Successfully enrolled :enrolled new student(s) and updated :updated existing enrollment(s).", [
                                'enrolled' => $enrolledCount,
                                'updated' => $updatedCount,
                            ]))
                            ->success()
                            ->send();
                    }),

                CreateAction::make()
                    ->label(__('Add Single Student'))
                    ->modalHeading(__('Enroll Student in Course')),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->label(__('Unenroll'))
                    ->modalHeading(__('Unenroll Student from Course'))
                    ->modalDescription(__('Are you sure you want to remove this student from the course? Their progress and submissions will be detached.')),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('change_cohort')
                        ->label(__('Assign Cohort'))
                        ->icon('heroicon-o-tag')
                        ->color('primary')
                        ->schema([
                            TextInput::make('new_cohort')
                                ->label(__('New Cohort / Group Name'))
                                ->placeholder(__('e.g. Group B, Advanced Cohort'))
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            $records->each(fn (CourseEnrollment $record) => $record->update(['cohort' => trim($data['new_cohort'])]));
                            Notification::make()
                                ->title(__('Cohort Updated'))
                                ->success()
                                ->send();
                        }),

                    BulkAction::make('mark_active')
                        ->label(__('Set Status: Active'))
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function (Collection $records): void {
                            $records->each(fn (CourseEnrollment $record) => $record->update(['status' => 'active']));
                            Notification::make()
                                ->title(__('Status Updated to Active'))
                                ->success()
                                ->send();
                        }),

                    BulkAction::make('mark_completed')
                        ->label(__('Set Status: Completed'))
                        ->icon('heroicon-o-academic-cap')
                        ->color('info')
                        ->action(function (Collection $records): void {
                            $records->each(fn (CourseEnrollment $record) => $record->update([
                                'status' => 'completed',
                                'progress_percent' => 100,
                                'completed_at' => now(),
                            ]));
                            Notification::make()
                                ->title(__('Marked Selected as Completed'))
                                ->success()
                                ->send();
                        }),

                    DeleteBulkAction::make()
                        ->label(__('Unenroll Selected Students')),
                ]),
            ]);
    }
}
