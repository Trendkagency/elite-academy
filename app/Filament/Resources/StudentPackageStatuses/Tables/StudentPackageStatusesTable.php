<?php

namespace App\Filament\Resources\StudentPackageStatuses\Tables;

use App\Models\PackageTemplate;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class StudentPackageStatusesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('studentUser.studentProfile.avatar')
                    ->label(__('الصورة'))
                    ->circular()
                    ->getStateUsing(fn ($record) => $record->studentUser?->studentProfile?->avatar_url)
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->studentUser?->name ?? 'Student') . '&background=0D9488&color=fff')
                    ->toggleable(),

                TextColumn::make('studentUser.name')
                    ->label(__('اسم الطالب'))
                    ->weight('bold')
                    ->searchable()
                    ->sortable()
                    ->description(function ($record) {
                        $email = $record->studentUser?->email ?? '';
                        $grade = $record->studentUser?->studentProfile?->gradeLevel?->name;
                        return $grade ? "{$grade} • {$email}" : $email;
                    })
                    ->wrap(),

                TextColumn::make('contact_info')
                    ->label(__('بيانات الاتصال والتواصل'))
                    ->state(function ($record) {
                        $studentPhone = $record->studentUser?->phone;
                        $parent = $record->studentUser?->parents?->first();
                        $parentName = $parent?->name;
                        $parentPhone = $parent?->phone;

                        $parts = [];
                        if ($studentPhone) {
                            $parts[] = "📱 طالب: {$studentPhone}";
                        }
                        if ($parentPhone) {
                            $label = $parentName ? "ولي أمر ({$parentName})" : 'ولي أمر';
                            $parts[] = "👨‍👧 {$label}: {$parentPhone}";
                        }

                        return count($parts) ? implode("\n", $parts) : __('لا يوجد رقم مسجل');
                    })
                    ->wrap()
                    ->lineClamp(2),

                TextColumn::make('packageTemplate.name')
                    ->label(__('خطة الباقة'))
                    ->placeholder(__('— باقة مخصصة —'))
                    ->badge()
                    ->color('info')
                    ->searchable(),

                TextColumn::make('remaining_sessions')
                    ->label(__('الرصيد المتبقي'))
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state <= 0 => 'danger',
                        $state <= 2 => 'warning',
                        default     => 'success',
                    })
                    ->formatStateUsing(fn (int $state) => match (true) {
                        $state <= 0 => "🔴 0 حصص (نفد الرصيد)",
                        $state <= 2 => "⚠️ {$state} حصص متبقية",
                        default     => "🟢 {$state} حصص",
                    })
                    ->sortable(),

                TextColumn::make('sessions_progress')
                    ->label(__('الاستهلاك / الإجمالي'))
                    ->state(fn ($record) => "{$record->used_sessions} / {$record->total_sessions} حصة")
                    ->description(function ($record) {
                        if ($record->total_sessions > 0) {
                            $pct = round(($record->used_sessions / $record->total_sessions) * 100);
                            return "مستهلك: {$pct}%";
                        }
                        return null;
                    })
                    ->sortable(query: fn ($query, $direction) => $query->orderBy('used_sessions', $direction)),

                TextColumn::make('status_reason')
                    ->label(__('حالة التنبيه والاشتراك'))
                    ->badge()
                    ->state(function ($record) {
                        $isExhausted = $record->remaining_sessions <= 0 || $record->status === 'exhausted';
                        $isPastExpiry = $record->expires_at && $record->expires_at->isPast();
                        $isLowBalance = $record->remaining_sessions > 0 && $record->remaining_sessions <= 2;

                        if ($isExhausted && $isPastExpiry) {
                            return __('نفد الرصيد وتجاوزت التاريخ');
                        } elseif ($isExhausted) {
                            return __('نفد الرصيد بالكامل (0 حصص)');
                        } elseif ($isPastExpiry) {
                            return __('تجاوزت تاريخ الصلاحية');
                        } elseif ($isLowBalance) {
                            return __('رصيد منخفض (1-2 حصص)');
                        }
                        return __(ucfirst($record->status));
                    })
                    ->color(function ($record) {
                        if ($record->remaining_sessions <= 0 || $record->status === 'exhausted') {
                            return 'danger';
                        }
                        if ($record->expires_at && $record->expires_at->isPast()) {
                            return 'danger';
                        }
                        if ($record->remaining_sessions <= 2) {
                            return 'warning';
                        }
                        return 'success';
                    })
                    ->icon(function ($record) {
                        if ($record->remaining_sessions <= 0 || $record->status === 'exhausted') {
                            return 'heroicon-m-x-circle';
                        }
                        if ($record->expires_at && $record->expires_at->isPast()) {
                            return 'heroicon-m-clock';
                        }
                        if ($record->remaining_sessions <= 2) {
                            return 'heroicon-m-exclamation-triangle';
                        }
                        return 'heroicon-m-check-circle';
                    }),

                TextColumn::make('expires_at')
                    ->label(__('تاريخ الصلاحية'))
                    ->dateTime('d M Y')
                    ->placeholder(__('غير محدد'))
                    ->sortable()
                    ->description(function ($record) {
                        if (! $record->expires_at) {
                            return null;
                        }
                        if ($record->expires_at->isPast()) {
                            return __('منتهي منذ :diff', ['diff' => $record->expires_at->diffForHumans(['parts' => 1])]);
                        }
                        return __('متبقي :diff', ['diff' => $record->expires_at->diffForHumans(['parts' => 1])]);
                    })
                    ->color(fn ($record) => $record->expires_at && $record->expires_at->isPast() ? 'danger' : null),

                TextColumn::make('course.title')
                    ->label(__('المقرر المرتبط'))
                    ->placeholder(__('كافة المقررات'))
                    ->limit(25)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('activated_at')
                    ->label(__('تاريخ التفعيل'))
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('alert_type')
                    ->label(__('نوع التنبيه'))
                    ->options([
                        'exhausted' => __('نفد الرصيد بالكامل (0 حصص)'),
                        'expired'   => __('تجاوزت تاريخ الصلاحية'),
                        'low'       => __('رصيد منخفض (1-2 حصص متبقية)'),
                        'active'    => __('باقات نشطة برصيد متاح'),
                    ])
                    ->query(function ($query, array $data) {
                        return match ($data['value'] ?? null) {
                            'exhausted' => $query->where(fn ($q) => $q->where('remaining_sessions', '<=', 0)->orWhere('status', 'exhausted')),
                            'expired'   => $query->whereNotNull('expires_at')->where('expires_at', '<', now()),
                            'low'       => $query->where('remaining_sessions', '>', 0)->where('remaining_sessions', '<=', 2),
                            'active'    => $query->where('status', 'active')->where('remaining_sessions', '>', 2),
                            default     => $query,
                        };
                    }),

                SelectFilter::make('package_template_id')
                    ->label(__('خطة الباقة'))
                    ->relationship('packageTemplate', 'name')
                    ->preload(),
            ])
            ->recordActions([
                // ── 1. RENEW PACKAGE MODAL (FULL LOGIC) ─────────────────────
                Action::make('renewPackage')
                    ->label(__('تجديد الباقة فوراً'))
                    ->icon('heroicon-o-arrow-path')
                    ->color('primary')
                    ->modalHeading(fn ($record) => __('تجديد وتفعيل باقة الطالب') . " — " . ($record->studentUser?->name ?? ''))
                    ->modalDescription(fn ($record) => "الرصيد الحالي: {$record->remaining_sessions} / {$record->total_sessions} حصة. عند التجديد، سيتم تصفير المستهلك وشحن الحصص الجديدة فوراً وتحويل الحالة إلى نشطة.")
                    ->form([
                        Section::make(__('بيانات خطة التجديد'))
                            ->description(__('اختر قالباً جاهزاً لملء الحصص تلقائياً أو أدخل العدد يدوياً'))
                            ->schema([
                                Select::make('package_template_id')
                                    ->label(__('قالب الباقة (اختياري)'))
                                    ->options(fn () => PackageTemplate::where('is_active', true)
                                        ->get()
                                        ->mapWithKeys(fn ($t) => [$t->id => "{$t->name} — {$t->sessions_count} حصة (" . number_format($t->price, 2) . " ج.م)"])
                                        ->toArray()
                                    )
                                    ->live()
                                    ->afterStateUpdated(function ($state, Set $set) {
                                        if ($state) {
                                            $template = PackageTemplate::find($state);
                                            if ($template) {
                                                $set('new_total_sessions', $template->sessions_count);
                                                if ($template->validity_days) {
                                                    $set('new_expires_at', now()->addDays($template->validity_days)->format('Y-m-d H:i:s'));
                                                }
                                            }
                                        }
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->nullable()
                                    ->native(false),

                                TextInput::make('new_total_sessions')
                                    ->label(__('عدد حصص الباقة الجديدة'))
                                    ->numeric()
                                    ->minValue(1)
                                    ->required()
                                    ->default(fn ($record) => $record->total_sessions > 0 ? $record->total_sessions : 12)
                                    ->suffix(__('حصة'))
                                    ->helperText(__('سيصبح هذا هو الرصيد الكلي والمتبقي الجديد للطالب.')),
                            ]),

                        Section::make(__('صلاحية الباقة وملاحظات التجديد'))
                            ->columns(2)
                            ->schema([
                                DateTimePicker::make('new_expires_at')
                                    ->label(__('تاريخ انتهاء الصلاحية الجديد'))
                                    ->nullable()
                                    ->default(fn ($record) => now()->addMonth())
                                    ->helperText(__('اتركه فارغاً إذا كانت الباقة بدون تاريخ انتهاء محدد.')),

                                TextInput::make('renewal_reason')
                                    ->label(__('سبب التجديد / مرجع الدفع'))
                                    ->default('تجديد باقة الحصص وتحصيل الرسوم')
                                    ->required()
                                    ->maxLength(200),
                            ]),
                    ])
                    ->action(function ($record, array $data) {
                        $newExpiry = ! empty($data['new_expires_at'])
                            ? \Carbon\Carbon::parse($data['new_expires_at'])
                            : null;

                        $record->renewPackage(
                            newTotalSessions: (int) $data['new_total_sessions'],
                            packageTemplateId: $data['package_template_id'] ?? null,
                            newExpiresAt: $newExpiry,
                            reason: $data['renewal_reason'] ?? 'تجديد باقة الحصص وتحصيل الاشتراك',
                        );

                        Notification::make()
                            ->title(__('تم تجديد الباقة بنجاح'))
                            ->body("تم شحن {$data['new_total_sessions']} حصة للطالب {$record->studentUser?->name} وتفعيل الباقة بنجاح.")
                            ->success()
                            ->send();
                    }),

                // ── 2. WHATSAPP 1-CLICK REMINDER ────────────────────────────
                Action::make('sendWhatsAppReminder')
                    ->label(__('مراسلة واتساب'))
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('success')
                    ->url(function ($record) {
                        // Prioritize parent phone, otherwise student phone
                        $parent = $record->studentUser?->parents?->first();
                        $targetPhone = $parent?->phone ?: $record->studentUser?->phone;

                        if (! $targetPhone) {
                            return null;
                        }

                        // Clean phone number (remove spaces, +, dashes)
                        $cleanPhone = preg_replace('/[^0-9]/', '', $targetPhone);
                        if (str_starts_with($cleanPhone, '01')) {
                            $cleanPhone = '20' . substr($cleanPhone, 1); // Egyptian prefix fallback
                        }

                        $studentName = $record->studentUser?->name ?? 'الطالب';
                        $packageName = $record->packageTemplate?->name ?? 'باقة الحصص';
                        $used = $record->used_sessions;
                        $total = $record->total_sessions;

                        $msg = "السلام عليكم ورحمة الله وبركاته،\n"
                             . "إدارة منصة أكاديمية إيليت التعليمية Elite Academy تحييكم.\n\n"
                             . "نود إحاطتكم علماً بأن باقة الحصص الخاصة بالطالب ({$studentName}) قد انتهت بالكامل (تم استهلاك {$used} من أصل {$total} حصة في {$packageName}).\n\n"
                             . "نرجو من حضراتكم سرعة التواصل معنا لتجديد الاشتراك وشحن الحصص لضمان مواصلة الجدول الدراسي ومواعيد الحصص المباشرة بدون انقطاع.\n\n"
                             . "شاكرين ومقدرين حسن تعاونكم الدائم معنا.";

                        return "https://api.whatsapp.com/send?phone={$cleanPhone}&text=" . urlencode($msg);
                    })
                    ->openUrlInNewTab()
                    ->visible(function ($record) {
                        $parent = $record->studentUser?->parents?->first();
                        return filled($parent?->phone) || filled($record->studentUser?->phone);
                    }),

                // ── 3. ADD EXTRA CREDITS (+TOP-UP) ──────────────────────────
                Action::make('addCredits')
                    ->label(__('إضافة حصص تعويضية'))
                    ->icon('heroicon-o-plus-circle')
                    ->color('warning')
                    ->form([
                        TextInput::make('count')
                            ->label(__('عدد الحصص الإضافية'))
                            ->numeric()
                            ->minValue(1)
                            ->default(2)
                            ->required()
                            ->suffix(__('حصة')),

                        Textarea::make('note')
                            ->label(__('ملاحظات الإضافة'))
                            ->default(__('إضافة حصص استثنائية لحين تجديد الاشتراك'))
                            ->rows(2),
                    ])
                    ->action(function ($record, array $data) {
                        $n = (int) $data['count'];
                        $record->increment('remaining_sessions', $n);
                        $record->increment('total_sessions', $n);

                        if ($record->remaining_sessions > 0 && in_array($record->status, ['exhausted', 'pending'])) {
                            $record->update(['status' => 'active']);
                        }

                        Notification::make()
                            ->title(__('تمت إضافة :count حصة للطالب :name', ['count' => $n, 'name' => $record->studentUser?->name]))
                            ->success()
                            ->send();
                    }),

                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // Bulk actions
                ]),
            ])
            ->defaultSort('remaining_sessions', 'asc')
            ->striped()
            ->emptyStateHeading(__('لا توجد باقات منتهية حالياً'))
            ->emptyStateDescription(__('رائع! كافة الطلاب لديهم باقات نشطة ومستمرة برصيد متاح.'))
            ->emptyStateIcon('heroicon-o-check-badge');
    }
}
