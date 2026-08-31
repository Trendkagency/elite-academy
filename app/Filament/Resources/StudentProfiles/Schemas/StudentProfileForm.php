<?php

namespace App\Filament\Resources\StudentProfiles\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;

class StudentProfileForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('👤 Student User Account Credentials & Status')
                    ->description('Manage student user profile, account status, email, and phone credentials')
                    ->columns(2)
                    ->columnSpanFull()
                    ->components([
                        Select::make('user_id')
                            ->relationship('user')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} ({$record->email})")
                            ->label('Student Account User')
                            ->searchable(['name', 'email', 'phone'])
                            ->preload()
                            ->required(),

                        Select::make('user.status')
                            ->relationship('user', 'status')
                            ->label('Account Approval Status')
                            ->options([
                                'pending' => '⏳ Pending Approval (قيد المراجعة)',
                                'approved' => '✅ Approved (مقبول)',
                                'rejected' => '❌ Rejected (مرفوض)',
                                'suspended' => '🚫 Suspended (معلق)',
                            ])
                            ->required(),
                    ]),

                Section::make('🏫 Academic Profile & School Metadata')
                    ->description('Manage grade level, school name, and free trial session flag')
                    ->columns(2)
                    ->columnSpanFull()
                    ->components([
                        Select::make('grade_level_id')
                            ->relationship('gradeLevel', 'name')
                            ->label('Grade Level')
                            ->searchable()
                            ->preload(),

                        TextInput::make('school_name')
                            ->label('School Name')
                            ->placeholder('e.g. Al-Bayan International School'),

                        DatePicker::make('date_of_birth')
                            ->label('Date of Birth'),

                        Toggle::make('has_used_free_session')
                            ->label('Used Free Trial Session')
                            ->helperText('Flag indicating if student consumed their free trial session credit.'),
                    ]),

                Section::make('📊 360° Student Academic Overview, Packages & Submissions')
                    ->description('Real-time overview of student active session package credits, linked parents, enrolled courses, and homework submissions')
                    ->columnSpanFull()
                    ->components([
                        View::make('filament.resources.student-profile.academic-overview'),
                    ]),
            ]);
    }
}
