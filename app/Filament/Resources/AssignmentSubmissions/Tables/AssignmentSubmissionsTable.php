<?php

namespace App\Filament\Resources\AssignmentSubmissions\Tables;

use App\Actions\Submission\GradeSubmissionAction;
use App\Enums\SubmissionStatus;
use App\Models\AssignmentSubmission;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class AssignmentSubmissionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('assignment.title')
                    ->label(app()->getLocale() === 'ar' ? 'عنوان الواجب / الاختبار' : 'Assignment Title')
                    ->weight('bold')
                    ->searchable()
                    ->sortable()
                    ->description(function (AssignmentSubmission $record): string {
                        $course = $record->assignment?->course?->title;
                        $subject = $record->assignment?->course?->subject?->name;
                        if ($course && $subject) {
                            return "📚 {$course} ({$subject})";
                        }
                        return $course ? "📚 {$course}" : '—';
                    }),

                TextColumn::make('studentUser.name')
                    ->label(app()->getLocale() === 'ar' ? 'اسم الطالب' : 'Student Name')
                    ->searchable()
                    ->sortable()
                    ->description(fn (AssignmentSubmission $record): ?string => $record->studentUser?->email),

                TextColumn::make('grade')
                    ->label(app()->getLocale() === 'ar' ? 'الدرجة / النسبة' : 'Score / Grade')
                    ->badge()
                    ->state(function (AssignmentSubmission $record): string {
                        $score = $record->percentage ?? $record->grade ?? $record->score ?? 0;
                        $pts = $record->score !== null ? round($record->score, 1) : null;
                        $totalPts = $record->total_points !== null ? round($record->total_points, 1) : null;
                        
                        if ($pts !== null && $totalPts !== null) {
                            return "{$score}% ({$pts}/{$totalPts} pts)";
                        }
                        return "{$score}%";
                    })
                    ->color(function (AssignmentSubmission $record): string {
                        return $record->isPassed() ? 'success' : 'danger';
                    })
                    ->icon(function (AssignmentSubmission $record): string {
                        return $record->isPassed() ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle';
                    })
                    ->sortable(),

                TextColumn::make('status')
                    ->label(app()->getLocale() === 'ar' ? 'الحالة' : 'Status')
                    ->badge()
                    ->formatStateUsing(function ($state): string {
                        $val = is_object($state) ? $state->value : (string) $state;
                        return match ($val) {
                            'completed' => app()->getLocale() === 'ar' ? 'مكتمل (ناجح)' : 'Completed (Passed)',
                            'reviewed' => app()->getLocale() === 'ar' ? 'تم التقييم' : 'Reviewed',
                            'submitted' => app()->getLocale() === 'ar' ? 'تم التسليم' : 'Submitted',
                            'late' => app()->getLocale() === 'ar' ? 'تسليم متأخر' : 'Late',
                            'in_progress' => app()->getLocale() === 'ar' ? 'جاري الحل' : 'In Progress',
                            default => app()->getLocale() === 'ar' ? 'قيد المراجعة' : 'Pending',
                        };
                    })
                    ->color(fn ($state): string => match (is_object($state) ? $state->value : (string) $state) {
                        'completed' => 'success',
                        'reviewed' => 'primary',
                        'submitted' => 'info',
                        'late' => 'warning',
                        'in_progress' => 'gray',
                        default => 'gray',
                    }),

                TextColumn::make('time_spent')
                    ->label(app()->getLocale() === 'ar' ? 'المدة المستغرقة' : 'Duration')
                    ->state(function (AssignmentSubmission $record): string {
                        if ($record->started_at && $record->submitted_at) {
                            $diffMin = $record->started_at->diffInMinutes($record->submitted_at);
                            $diffSec = $record->started_at->diffInSeconds($record->submitted_at) % 60;
                            return $diffMin > 0 ? "{$diffMin}m {$diffSec}s" : "{$diffSec}s";
                        }
                        return '—';
                    })
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('submitted_at')
                    ->label(app()->getLocale() === 'ar' ? 'تاريخ التسليم' : 'Submitted At')
                    ->dateTime('Y-m-d h:i A')
                    ->description(fn (AssignmentSubmission $record): ?string => $record->submitted_at ? $record->submitted_at->diffForHumans() : null)
                    ->sortable(),

                TextColumn::make('reviewer.name')
                    ->label(app()->getLocale() === 'ar' ? 'تم التقييم بواسطة' : 'Reviewed By')
                    ->placeholder('— (Auto)')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label(app()->getLocale() === 'ar' ? 'تاريخ الإنشاء' : 'Created At')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label(app()->getLocale() === 'ar' ? 'آخر تحديث' : 'Updated At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('assignment_id')
                    ->label(app()->getLocale() === 'ar' ? 'الواجب / الاختبار' : 'Assignment')
                    ->relationship('assignment', 'title')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('course_id')
                    ->label(app()->getLocale() === 'ar' ? 'الكورس الدراسي' : 'Course')
                    ->options(fn () => \App\Models\Course::pluck('title', 'id')->toArray())
                    ->query(function ($query, array $data) {
                        if (! empty($data['value'])) {
                            $query->whereHas('assignment', fn ($q) => $q->where('course_id', $data['value']));
                        }
                    }),

                SelectFilter::make('status')
                    ->label(app()->getLocale() === 'ar' ? 'حالة التسليم' : 'Status')
                    ->options([
                        'completed' => app()->getLocale() === 'ar' ? 'مكتمل (ناجح)' : 'Completed (Passed)',
                        'reviewed' => app()->getLocale() === 'ar' ? 'تم التقييم' : 'Reviewed',
                        'submitted' => app()->getLocale() === 'ar' ? 'تم التسليم' : 'Submitted',
                        'late' => app()->getLocale() === 'ar' ? 'تسليم متأخر' : 'Late',
                        'in_progress' => app()->getLocale() === 'ar' ? 'جاري الحل' : 'In Progress',
                        'pending' => app()->getLocale() === 'ar' ? 'قيد المراجعة' : 'Pending',
                    ]),

                TrashedFilter::make(),
            ])
            ->recordActions([
                Action::make('viewDetails')
                    ->label(app()->getLocale() === 'ar' ? 'عرض التفاصيل والإجابات' : 'View Details & Answers')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->modalWidth('7xl')
                    ->modalHeading(app()->getLocale() === 'ar' ? 'تفاصيل تسليم الواجب وإجابات الطالب' : 'Assignment Submission & Student Answers')
                    ->modalSubheading(app()->getLocale() === 'ar' ? 'مراجعة شاملة لبيانات الواجب، أداء الطالب، وتفاصيل الأسئلة والإجابات المعتمدة.' : 'Review assignment information, student performance, and question-by-question answer keys.')
                    ->modalContent(function (AssignmentSubmission $record) {
                        $record->loadMissing([
                            'assignment.course.subject',
                            'assignment.course.teacher',
                            'assignment.session',
                            'assignment.liveSession',
                            'assignment.teacherProfile.user',
                            'assignment.questions.options',
                            'studentUser.studentProfile',
                            'enrollment.course',
                            'reviewer',
                            'answers.question.options',
                        ]);

                        return view('filament.resources.assignment-submissions.submission-details-modal', [
                            'submission' => $record,
                        ]);
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel(app()->getLocale() === 'ar' ? 'إغلاق' : 'Close'),

                Action::make('gradeSubmission')
                    ->label(app()->getLocale() === 'ar' ? 'تقييم وتعديل الدرجة' : 'Grade & Review')
                    ->icon('heroicon-o-academic-cap')
                    ->color('success')
                    ->modalHeading(app()->getLocale() === 'ar' ? 'تقييم تسليم الطالب وتحديث الدرجة' : 'Evaluate & Grade Submission')
                    ->form([
                        TextInput::make('grade')
                            ->label(app()->getLocale() === 'ar' ? 'الدرجة بالنسبة المئوية (%)' : 'Grade Percentage (%)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->suffix('%')
                            ->default(fn (AssignmentSubmission $record) => (int) ($record->grade ?? $record->percentage ?? 100))
                            ->required(),

                        Select::make('status')
                            ->label(app()->getLocale() === 'ar' ? 'حالة التسليم' : 'Submission Status')
                            ->options([
                                'completed' => app()->getLocale() === 'ar' ? 'Completed (ناجح ومكتمل)' : 'Completed (Passed)',
                                'reviewed' => app()->getLocale() === 'ar' ? 'Reviewed (تم التقييم)' : 'Reviewed',
                                'submitted' => app()->getLocale() === 'ar' ? 'Submitted (تم التسليم)' : 'Submitted',
                                'late' => app()->getLocale() === 'ar' ? 'Late (تسليم متأخر)' : 'Late Submission',
                                'pending' => app()->getLocale() === 'ar' ? 'Pending (قيد المراجعة)' : 'Pending Review',
                            ])
                            ->default(fn (AssignmentSubmission $record) => is_object($record->status) ? $record->status->value : ($record->status ?: 'completed'))
                            ->required(),

                        Textarea::make('teacher_notes')
                            ->label(app()->getLocale() === 'ar' ? 'ملاحظات وتوجيهات المعلم للطالب' : 'Teacher Feedback & Instructions')
                            ->placeholder(app()->getLocale() === 'ar' ? 'اكتب ملاحظاتك وتوجيهاتك للطالب هنا...' : 'Write evaluation feedback and instructions for the student...')
                            ->rows(3)
                            ->default(fn (AssignmentSubmission $record) => $record->teacher_notes),
                    ])
                    ->action(function (AssignmentSubmission $record, array $data): void {
                        $grade = (int) $data['grade'];
                        $feedback = $data['teacher_notes'] ?? null;
                        $status = $data['status'] ?? SubmissionStatus::COMPLETED->value;

                        // Execute core domain grading logic
                        try {
                            app(GradeSubmissionAction::class)->execute($record, $grade, $feedback);
                        } catch (\Throwable $e) {
                            // Fallback direct update if GradeSubmissionAction has dependency constraints
                            $passingScore = (float) ($record->passing_score ?? $record->assignment?->passing_score ?? $record->assignment?->passing_grade ?? 70.0);
                            $record->update([
                                'grade' => $grade,
                                'percentage' => $grade,
                                'score' => $grade,
                                'status' => $status,
                                'teacher_notes' => $feedback,
                                'evaluation_notes' => $feedback,
                                'reviewed_at' => now(),
                                'reviewed_by' => auth()->id(),
                            ]);
                        }

                        // Explicitly set the reviewer and custom status if selected
                        $record->update([
                            'status' => $status,
                            'reviewed_at' => now(),
                            'reviewed_by' => auth()->id(),
                            'teacher_notes' => $feedback,
                        ]);

                        Notification::make()
                            ->title(app()->getLocale() === 'ar' ? 'تم تحديث تقييم ودرجة الواجب بنجاح' : 'Submission graded and reviewed successfully.')
                            ->success()
                            ->send();
                    }),

                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
