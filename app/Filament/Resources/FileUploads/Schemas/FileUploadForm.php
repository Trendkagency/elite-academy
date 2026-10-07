<?php

namespace App\Filament\Resources\FileUploads\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class FileUploadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make(__('File Details / تفاصيل وبيانات الملف'))
                    ->icon(Heroicon::OutlinedDocumentArrowUp)
                    ->description(__('Enter the file title, select its primary purpose, and upload the file / أدخل عنوان الملف وحدد الغرض منه وارفق المستند المطلوب'))
                    ->columns([
                        'default' => 1,
                        'sm' => 1,
                        'md' => 2,
                    ])
                    ->components([
                        TextInput::make('title')
                            ->label(__('File Title / عنوان الملف'))
                            ->placeholder(__('e.g. مذكرة مراجعة ليلة الامتحان - الفصل الدراسي الأول'))
                            ->required()
                            ->maxLength(200)
                            ->columnSpan([
                                'default' => 1,
                                'md' => 2,
                            ]),

                        Select::make('category')
                            ->label(__('File Purpose / Category (نوع الملف / الغرض)'))
                            ->options([
                                'material' => __('Study Material / مذكرة دراسية ومحتوى تعليمي'),
                                'homework' => __('Homework Assignment / واجب منزلي وتكليف'),
                                'submission' => __('Student Submission / تسليم وحل طالب'),
                            ])
                            ->default('material')
                            ->required()
                            ->live()
                            ->columnSpan(fn ($get) => $get('category') === 'homework' ? 1 : [
                                'default' => 1,
                                'md' => 2,
                            ]),

                        DateTimePicker::make('due_at')
                            ->label(__('Submission Deadline / آخر موعد للتسليم'))
                            ->required(fn ($get) => $get('category') === 'homework')
                            ->visible(fn ($get) => $get('category') === 'homework')
                            ->helperText(__('When saved as homework, students will see this deadline in their assignments hub / يظهر هذا الموعد للطلاب في قسم الواجبات والتكليفات.'))
                            ->columnSpan(1),

                        Textarea::make('description')
                            ->label(__('Description / Instructions (الوصف والتعليمات)'))
                            ->placeholder(__('أضف أية تفاصيل أو تعليمات موجهة للطلاب أو أولياء الأمور بخصوص هذا الملف أو الواجب...'))
                            ->rows(3)
                            ->nullable()
                            ->columnSpan([
                                'default' => 1,
                                'md' => 2,
                            ]),

                        FileUpload::make('file_path')
                            ->label(__('Upload Document or Image / رفع المستند أو الصورة'))
                            ->disk('public')
                            ->directory('educational_files')
                            ->acceptedFileTypes([
                                'application/pdf',
                                'image/jpeg',
                                'image/png',
                                'image/webp',
                                'image/gif',
                            ])
                            ->maxSize(25600) // 25 MB
                            ->preserveFilenames()
                            ->storeFileNamesIn('original_name')
                            ->required()
                            ->columnSpan([
                                'default' => 1,
                                'md' => 2,
                            ]),
                    ]),

                Section::make(__('Academic Associations & Target / الارتباط الأكاديمي والجمهور المستهدف'))
                    ->icon(Heroicon::OutlinedAcademicCap)
                    ->description(__('Link this educational file or homework to a specific course, teacher, or student / ربط الملف بمقرر دراسي أو معلم أو توجيهه لطالب محدد'))
                    ->columns([
                        'default' => 1,
                        'sm' => 1,
                        'md' => 2,
                    ])
                    ->components([
                        Select::make('course_id')
                            ->label(__('Course / المقرر أو الكورس الدراسـي'))
                            ->placeholder(__('Select Course / اختر المقرر الدراسي...'))
                            ->relationship(
                                name: 'course',
                                titleAttribute: 'title',
                                modifyQueryUsing: fn (\Illuminate\Database\Eloquent\Builder $query) => $query->with(['subject', 'gradeLevel', 'teacher.user'])->latest('created_at')
                            )
                            ->getOptionLabelFromRecordUsing(function ($record) {
                                $extra = $record->gradeLevel?->name ?? $record->subject?->name;
                                $teacher = $record->teacher?->user?->name;
                                $label = $record->title;
                                if ($extra) {
                                    $label .= " \u{200E}•\u{200E} {$extra}";
                                }
                                if ($teacher) {
                                    $label .= " \u{200E}—\u{200E} {$teacher}";
                                }
                                return $label;
                            })
                            ->searchable()
                            ->searchDebounce(200)
                            ->preload()
                            ->nullable()
                            ->getSearchResultsUsing(function (string $search): array {
                                $vars = function_exists('arabic_search_variations') ? arabic_search_variations($search) : [$search];

                                return \App\Models\Course::query()
                                    ->with(['subject', 'gradeLevel', 'teacher.user'])
                                    ->where(function ($query) use ($vars, $search) {
                                        $query->where(function ($q) use ($vars, $search) {
                                            foreach ($vars as $v) {
                                                $q->orWhere('title', 'like', "%{$v}%");
                                            }
                                            $q->orWhere('title', 'like', "%{$search}%");
                                        })
                                        ->orWhereHas('subject', function ($sq) use ($vars, $search) {
                                            $sq->where(function ($inner) use ($vars, $search) {
                                                foreach ($vars as $v) {
                                                    $inner->orWhere('name', 'like', "%{$v}%");
                                                }
                                                $inner->orWhere('name', 'like', "%{$search}%");
                                            });
                                        })
                                        ->orWhereHas('gradeLevel', function ($gq) use ($vars, $search) {
                                            $gq->where(function ($inner) use ($vars, $search) {
                                                foreach ($vars as $v) {
                                                    $inner->orWhere('name', 'like', "%{$v}%");
                                                }
                                                $inner->orWhere('name', 'like', "%{$search}%");
                                            });
                                        })
                                        ->orWhereHas('teacher.user', function ($tq) use ($vars, $search) {
                                            $tq->where(function ($inner) use ($vars, $search) {
                                                foreach ($vars as $v) {
                                                    $inner->orWhere('name', 'like', "%{$v}%");
                                                }
                                                $inner->orWhere('name', 'like', "%{$search}%");
                                            });
                                        });
                                    })
                                    ->latest('created_at')
                                    ->limit(50)
                                    ->get()
                                    ->mapWithKeys(function ($record) {
                                        $extra = $record->gradeLevel?->name ?? $record->subject?->name;
                                        $teacher = $record->teacher?->user?->name;
                                        $label = $record->title;
                                        if ($extra) {
                                            $label .= " \u{200E}•\u{200E} {$extra}";
                                        }
                                        if ($teacher) {
                                            $label .= " \u{200E}—\u{200E} {$teacher}";
                                        }
                                        return [$record->id => $label];
                                    })
                                    ->toArray();
                            })
                            ->columnSpan(1),

                        Select::make('teacher_profile_id')
                            ->label(__('Teacher / المعلم المشرف المسؤول'))
                            ->placeholder(__('Select Teacher / اختر المعلم...'))
                            ->relationship(
                                name: 'teacherProfile',
                                titleAttribute: 'id',
                                modifyQueryUsing: fn (\Illuminate\Database\Eloquent\Builder $query) => $query->with('user')->latest('created_at')
                            )
                            ->getOptionLabelFromRecordUsing(fn ($record) => ($record->user?->name ?: 'Teacher #' . $record->id) . ($record->specialization ? " \u{200E}•\u{200E} " . $record->specialization : ''))
                            ->searchable()
                            ->searchDebounce(200)
                            ->preload()
                            ->nullable()
                            ->getSearchResultsUsing(function (string $search): array {
                                $vars = function_exists('arabic_search_variations') ? arabic_search_variations($search) : [$search];

                                return \App\Models\TeacherProfile::query()
                                    ->with('user')
                                    ->where(function ($query) use ($vars, $search) {
                                        $query->whereHas('user', function ($uq) use ($vars, $search) {
                                            $uq->where(function ($inner) use ($vars, $search) {
                                                foreach ($vars as $v) {
                                                    $inner->orWhere('name', 'like', "%{$v}%")
                                                          ->orWhere('email', 'like', "%{$v}%");
                                                }
                                                $inner->orWhere('name', 'like', "%{$search}%")
                                                      ->orWhere('email', 'like', "%{$search}%");
                                            });
                                        })
                                        ->orWhere(function ($sq) use ($vars, $search) {
                                            foreach ($vars as $v) {
                                                $sq->orWhere('specialization', 'like', "%{$v}%");
                                            }
                                            $sq->orWhere('specialization', 'like', "%{$search}%");
                                        });
                                    })
                                    ->latest('created_at')
                                    ->limit(50)
                                    ->get()
                                    ->mapWithKeys(function ($teacher) {
                                        $name = $teacher->user?->name ?: 'Teacher #' . $teacher->id;
                                        $spec = $teacher->specialization ? " \u{200E}•\u{200E} " . $teacher->specialization : '';
                                        return [$teacher->id => $name . $spec];
                                    })
                                    ->toArray();
                            })
                            ->columnSpan(1),

                        Select::make('student_user_id')
                            ->label(__('Specific Student (Optional) / تخصيص لطالب معين (اختياري)'))
                            ->placeholder(__('Select Student / اختر طالباً محدداً...'))
                            ->relationship(
                                name: 'studentUser',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (\Illuminate\Database\Eloquent\Builder $query) => $query->whereHas('studentProfile')->latest('created_at')
                            )
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} \u{200E}•\u{200E} \u{2066}{$record->email}\u{2069}")
                            ->searchable()
                            ->searchDebounce(200)
                            ->preload()
                            ->nullable()
                            ->getSearchResultsUsing(function (string $search): array {
                                $vars = function_exists('arabic_search_variations') ? arabic_search_variations($search) : [$search];

                                return \App\Models\User::query()
                                    ->whereHas('studentProfile')
                                    ->where(function ($query) use ($vars, $search) {
                                        $query->where(function ($inner) use ($vars, $search) {
                                            foreach ($vars as $v) {
                                                $inner->orWhere('name', 'like', "%{$v}%")
                                                      ->orWhere('email', 'like', "%{$v}%")
                                                      ->orWhere('phone', 'like', "%{$v}%");
                                            }
                                            $inner->orWhere('name', 'like', "%{$search}%")
                                                  ->orWhere('email', 'like', "%{$search}%")
                                                  ->orWhere('phone', 'like', "%{$search}%");
                                        });
                                    })
                                    ->latest('created_at')
                                    ->limit(50)
                                    ->get()
                                    ->mapWithKeys(function ($student) {
                                        return [$student->id => "{$student->name} \u{200E}•\u{200E} \u{2066}{$student->email}\u{2069}"];
                                    })
                                    ->toArray();
                            })
                            ->helperText(__('Leave blank to make this file accessible to all enrolled students / اتركه فارغاً لإتاحة الملف لكافة الطلاب المسجلين بالمقرر.'))
                            ->columnSpan(fn ($get) => $get('category') === 'homework' ? 1 : [
                                'default' => 1,
                                'md' => 2,
                            ]),

                        Select::make('assignment_id')
                            ->label(__('Linked Assignment / الواجب والتكليف المرتبط (Optional)'))
                            ->placeholder(__('Select Assignment / اختر واجباً مرتبطاً...'))
                            ->relationship('assignment', 'title')
                            ->searchable()
                            ->searchDebounce(200)
                            ->preload()
                            ->nullable()
                            ->getSearchResultsUsing(function (string $search): array {
                                $vars = function_exists('arabic_search_variations') ? arabic_search_variations($search) : [$search];

                                return \App\Models\Assignment::query()
                                    ->where(function ($q) use ($vars, $search) {
                                        foreach ($vars as $v) {
                                            $q->orWhere('title', 'like', "%{$v}%");
                                        }
                                        $q->orWhere('title', 'like', "%{$search}%");
                                    })
                                    ->latest('created_at')
                                    ->limit(50)
                                    ->pluck('title', 'id')
                                    ->toArray();
                            })
                            ->visible(fn ($get) => $get('category') === 'homework')
                            ->helperText(__('Attach directly to an existing homework assignment if needed / ربط هذا الملف بتكليف دراسي موجود مسبقاً.'))
                            ->columnSpan(1),
                    ]),
            ]);
    }
}
