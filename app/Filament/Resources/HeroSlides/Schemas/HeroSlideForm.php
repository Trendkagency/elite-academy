<?php

namespace App\Filament\Resources\HeroSlides\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RangeSlider;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class HeroSlideForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                // ── SLIDE CONTENT ───────────────────────────────────────
                Section::make('📝 Slide Content')
                    ->description('Main visible text content for this slide.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->label(__('Headline / Title'))
                            ->required()
                            ->maxLength(120)
                            ->columnSpanFull()
                            ->placeholder('e.g. Empowering Future Leaders...'),

                        Textarea::make('subtitle')
                            ->label(__('Subtitle / Description'))
                            ->rows(3)
                            ->columnSpanFull()
                            ->placeholder('e.g. Join thousands of students learning...'),

                        TextInput::make('track_label')
                            ->label(__('Badge Label'))
                            ->placeholder('e.g. 🚀 EGYPT\'S #1 ACADEMIC PLATFORM')
                            ->maxLength(100),

                        TextInput::make('badge_icon')
                            ->label(__('Badge Icon (FA class)'))
                            ->placeholder('e.g. fa-solid fa-rocket')
                            ->maxLength(60),
                    ]),

                // ── CALL-TO-ACTION BUTTONS ───────────────────────────────
                Section::make('🔗 Call-to-Action Buttons')
                    ->description('Configure up to two action buttons. Leave URL blank to hide a button.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('cta_primary_url')
                            ->label(__('Primary Button URL'))
                            ->placeholder('/subjects'),

                        TextInput::make('cta_primary_text')
                            ->label(__('Primary Button Label'))
                            ->placeholder('Explore Now')
                            ->maxLength(60),

                        TextInput::make('cta_secondary_url')
                            ->label(__('Secondary Button URL'))
                            ->placeholder('/register'),

                        TextInput::make('cta_secondary_text')
                            ->label(__('Secondary Button Label'))
                            ->placeholder('Learn More')
                            ->maxLength(60),
                    ]),

                // ── BACKGROUND IMAGE ─────────────────────────────────────
                Section::make('🖼️ Background Image')
                    ->description('Upload a high-quality image (1920×1080 recommended). Supports drag & drop.')
                    ->schema([
                        FileUpload::make('image')
                            ->label(__('Slide Background Image'))
                            ->disk('public')
                            ->directory('hero-slides')
                            ->visibility('public')
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios(['16:9', '4:3', '3:2'])
                            ->imagePreviewHeight('220')
                            ->panelAspectRatio('16:9')
                            ->panelLayout('integrated')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/avif'])
                            ->maxSize(8192)
                            ->columnSpanFull(),
                    ]),

                // ── DESIGN & LAYOUT ──────────────────────────────────────
                Section::make('🎨 Design & Layout')
                    ->description('Control accent colors, overlay darkness, text alignment, and content position on the slide.')
                    ->columns(2)
                    ->schema([
                        Select::make('accent_color')
                            ->label(__('Accent Color'))
                            ->options([
                                'teal'   => '🩵 Teal (Default)',
                                'purple' => '💜 Purple',
                                'orange' => '🧡 Orange',
                                'rose'   => '🌹 Rose',
                                'sky'    => '🔵 Sky Blue',
                                'amber'  => '💛 Amber',
                            ])
                            ->default('teal')
                            ->native(false)
                            ->required(),

                        Select::make('text_align')
                            ->label(__('Text Alignment'))
                            ->options([
                                'left'   => '⬅️ Left',
                                'center' => '↔️ Center',
                                'right'  => '➡️ Right',
                            ])
                            ->default('left')
                            ->native(false)
                            ->required(),

                        Select::make('text_position')
                            ->label(__('Content Position on Slide'))
                            ->helperText('9-cell grid: where text block appears on the slide canvas.')
                            ->options([
                                'top-left'   => '↖ Top Left',
                                'top-center' => '⬆ Top Center',
                                'top-right'  => '↗ Top Right',
                                'mid-left'   => '⬅ Middle Left (Default)',
                                'mid-center' => '⊕ Middle Center',
                                'mid-right'  => '➡ Middle Right',
                                'bot-left'   => '↙ Bottom Left',
                                'bot-center' => '⬇ Bottom Center',
                                'bot-right'  => '↘ Bottom Right',
                            ])
                            ->default('mid-left')
                            ->native(false)
                            ->required(),

                        TextInput::make('overlay_opacity')
                            ->label(__('Overlay Darkness (0–100)'))
                            ->helperText('Higher = darker overlay. 55 is a good default.')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->default(55)
                            ->suffix('%'),
                    ]),

                // ── ORDER & STATUS ───────────────────────────────────────
                Section::make('⚙️ Order & Visibility')
                    ->columns(2)
                    ->schema([
                        TextInput::make('sort_order')
                            ->label(__('Sort Order'))
                            ->numeric()
                            ->default(0)
                            ->helperText('Lower number = shown first.'),

                        Toggle::make('is_active')
                            ->label(__('Active / Visible'))
                            ->default(true)
                            ->inline(false),
                    ]),

            ]);
    }
}