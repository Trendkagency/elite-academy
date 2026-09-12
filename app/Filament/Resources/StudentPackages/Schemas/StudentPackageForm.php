<?php

namespace App\Filament\Resources\StudentPackages\Schemas;

use App\Models\PackageTemplate;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StudentPackageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                // ── SECTION 1: Who Gets the Package ─────────────────────────
                Section::make(__('Student Information'))
                    ->icon('heroicon-o-user')
                    ->description(__('Select the student who will receive the package.'))
                    ->columnSpanFull()
                    ->schema([
                        Select::make('student_user_id')
                            ->label(__('Student'))
                            ->relationship(
                                name: 'studentUser',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn ($query) => $query->whereHas('studentProfile')->latest('created_at')
                            )
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} ({$record->email})")
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpanFull()
                            ->helperText(__('Search by student name or email address.')),
                    ]),

                // ── SECTION 2: Package Template (auto-fills credits) ─────────
                Section::make(__('Package Plan'))
                    ->icon('heroicon-o-cube')
                    ->description(__('Choose a pre-defined package template, or enter custom session credits manually below.'))
                    ->columnSpanFull()
                    ->schema([
                        Select::make('package_template_id')
                            ->label(__('Package Template'))
                            ->relationship('packageTemplate', 'name',
                                modifyQueryUsing: fn ($query) => $query->where('is_active', true)->latest('created_at')
                            )
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} — {$record->sessions_count} " . __('sessions') . ($record->price ? " ({$record->price} SAR)" : ''))
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->live()
                            ->afterStateUpdated(function ($state, Set $set) {
                                if ($state) {
                                    $template = PackageTemplate::find($state);
                                    if ($template) {
                                        $set('total_sessions', $template->sessions_count);
                                        $set('remaining_sessions', $template->sessions_count);
                                        $set('used_sessions', 0);
                                    }
                                }
                            })
                            ->columnSpanFull()
                            ->helperText(__('Selecting a template will automatically fill the session credits below.')),

                        Select::make('course_id')
                            ->label(__('Restrict to Course (Optional)'))
                            ->relationship('course', 'title', modifyQueryUsing: fn ($query) => $query->latest('created_at'))
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->helperText(__('Leave empty to allow access to all enrolled courses.'))
                            ->columnSpanFull(),
                    ]),

                // ── SECTION 3: Session Credits ───────────────────────────────
                Section::make(__('Session Credits'))
                    ->icon('heroicon-o-ticket')
                    ->description(__('Set the number of session credits for this package. These are auto-filled when a template is selected.'))
                    ->columnSpanFull()
                    ->columns(3)
                    ->schema([
                        TextInput::make('total_sessions')
                            ->label(__('Total Credits'))
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->default(12)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, Get $get, Set $set) {
                                $used = (int) ($get('used_sessions') ?? 0);
                                $total = (int) ($state ?? 0);
                                $set('remaining_sessions', max(0, $total - $used));
                            })
                            ->suffix(__('sessions')),

                        TextInput::make('used_sessions')
                            ->label(__('Already Used'))
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, Get $get, Set $set) {
                                $total = (int) ($get('total_sessions') ?? 0);
                                $used = (int) ($state ?? 0);
                                $set('remaining_sessions', max(0, $total - $used));
                            })
                            ->suffix(__('sessions')),

                        TextInput::make('remaining_sessions')
                            ->label(__('Remaining Credits'))
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->default(12)
                            ->helperText(__('Auto-computed: Total − Used. You can override manually.'))
                            ->suffix(__('sessions')),
                    ]),

                // ── SECTION 4: Status & Validity ─────────────────────────────
                Section::make(__('Activation & Validity'))
                    ->icon('heroicon-o-cog-6-tooth')
                    ->description(__('Set the package status and optional expiry date.'))
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        Select::make('status')
                            ->label(__('Package Status'))
                            ->options([
                                'active'    => __('Active — Student has access'),
                                'pending'   => __('Pending — Not yet activated'),
                                'exhausted' => __('Exhausted — 0 credits left'),
                                'suspended' => __('Suspended — Temporarily blocked'),
                            ])
                            ->default('active')
                            ->required()
                            ->native(false),

                        DateTimePicker::make('activated_at')
                            ->label(__('Activation Date'))
                            ->default(now())
                            ->helperText(__('When this package starts.')),

                        DateTimePicker::make('expires_at')
                            ->label(__('Expiry Date (Optional)'))
                            ->nullable()
                            ->helperText(__('Leave empty for no expiry.'))
                            ->columnSpan(1),
                    ]),
            ]);
    }
}
