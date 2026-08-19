<?php

namespace App\Filament\Resources\Testimonials\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class TestimonialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('Reviewer Name'))
                    ->required()
                    ->maxLength(255),
                Select::make('reviewer_type')
                    ->label(__('Reviewer Role'))
                    ->options([
                        'student' => 'Student (طالب)',
                        'parent' => 'Parent (ولي أمر)',
                    ])
                    ->default('student')
                    ->required(),
                TextInput::make('course_name')
                    ->label(__('Course / Track Name'))
                    ->placeholder('e.g. AI & Python Development')
                    ->maxLength(255),
                Select::make('rating')
                    ->label(__('Rating (1 to 5 Stars)'))
                    ->options([
                        5 => '⭐⭐⭐⭐⭐ (5 Stars)',
                        4 => '⭐⭐⭐⭐ (4 Stars)',
                        3 => '⭐⭐⭐ (3 Stars)',
                        2 => '⭐⭐ (2 Stars)',
                        1 => '⭐ (1 Star)',
                    ])
                    ->default(5)
                    ->required(),
                Textarea::make('content')
                    ->label(__('Review Feedback Content'))
                    ->required()
                    ->rows(4)
                    ->columnSpanFull(),
                FileUpload::make('avatar')
                    ->label(__('Reviewer Avatar (Drag & Drop Photo)'))
                    ->disk('public')
                    ->directory('testimonials')
                    ->visibility('public')
                    ->image()
                    ->imageEditor()
                    ->columnSpanFull(),
                TextInput::make('sort_order')
                    ->label(__('Sort Order'))
                    ->numeric()
                    ->default(0),
                Toggle::make('is_featured')
                    ->label(__('Show on Landing Page Featured Grid'))
                    ->default(true),
                Toggle::make('is_verified')
                    ->label(__('Verified Review Badge'))
                    ->default(true),
            ]);
    }
}