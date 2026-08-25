<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\TranslationStatsWidget;
use App\Jobs\TranslateTranslationJob;
use App\Models\TranslationKey;
use App\Models\TranslationValue;
use App\Services\Translation\MyMemoryTranslationService;
use App\Services\Translation\TranslationManagerService;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ManageTranslationSystem extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedLanguage;

    protected static ?int $navigationSort = 10;

    public static function getNavigationGroup(): ?string
    {
        return app()->getLocale() === 'ar' ? 'إعدادات النظام والترجمة' : 'Settings';
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'إدارة التراجم واللغات (Translation Management)' : 'Translation Management';
    }

    protected string $view = 'filament.pages.manage-translation-system';

    public function mount(): void
    {
        if (!Auth::user() || !Auth::user()->hasPermission(\App\Permissions\PermissionsRegistry::TRANSLATIONS_VIEW)) {
            abort(403, 'Unauthorized to view Translation Management.');
        }
    }

    protected function getHeaderWidgets(): array
    {
        return [
            TranslationStatsWidget::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('translate_all')
                ->label(app()->getLocale() === 'ar' ? '🤖 ترجمة الكل تلقائياً (Bulk Job)' : '🤖 Translate All (Bulk Job)')
                ->color('warning')
                ->icon(Heroicon::OutlinedSparkles)
                ->form([
                    Radio::make('bulk_mode')
                        ->label(__('Target Content Selection'))
                        ->options([
                            'missing' => __('Missing translations only (Recommended)'),
                        ])
                        ->default('missing')
                        ->required(),
                ])
                ->action(function (array $data) {
                    TranslateTranslationJob::dispatch($data['bulk_mode'] ?? 'missing', 'ar', 'en', Auth::id());

                    Notification::make()
                        ->title(__('Bulk Translation Queue Job Dispatched!'))
                        ->body(__('Background workers are translating missing items asynchronously.'))
                        ->success()
                        ->send();
                }),

            Action::make('clear_cache')
                ->label(app()->getLocale() === 'ar' ? '⚡ تفريغ كاش التراجم' : '⚡ Clear Translation Cache')
                ->color('gray')
                ->icon(Heroicon::OutlinedArrowPath)
                ->action(function () {
                    TranslationManagerService::clearCache();

                    Notification::make()
                        ->title(__('Translation Cache Cleared Successfully!'))
                        ->success()
                        ->send();
                }),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(TranslationKey::query()->with('values'))
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('key')
                    ->label(__('Translation Key'))
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->fontFamily('mono')
                    ->weight('bold')
                    ->color('primary')
                    ->wrap(),

                TextColumn::make('group')
                    ->label(__('Group'))
                    ->badge()
                    ->sortable()
                    ->color('gray'),

                TextColumn::make('ar_val')
                    ->label(__('Arabic Value (ar)'))
                    ->getStateUsing(fn (TranslationKey $record): string => $record->getValueForLocale('ar') ?? '— غير مترجم —')
                    ->color(fn (string $state): string => $state === '— غير مترجم —' ? 'danger' : 'gray')
                    ->wrap()
                    ->extraAttributes(['dir' => 'rtl', 'class' => 'font-sans']),

                TextColumn::make('en_val')
                    ->label(__('English Value (en)'))
                    ->getStateUsing(fn (TranslationKey $record): string => $record->getValueForLocale('en') ?? '— Missing EN —')
                    ->color(fn (string $state): string => $state === '— Missing EN —' ? 'danger' : 'gray')
                    ->wrap(),

                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->getStateUsing(function (TranslationKey $record): string {
                        $arVal = $record->getValueForLocale('ar');
                        $enVal = $record->getValueForLocale('en');
                        $arModel = $record->getTranslationValueModel('ar');
                        $enModel = $record->getTranslationValueModel('en');

                        if ($arModel?->is_locked || $enModel?->is_locked) {
                            return '🔒 Locked';
                        }
                        if (!empty($arVal) && !empty($enVal)) {
                            return '✓ Complete';
                        }
                        return '⚠ Missing';
                    })
                    ->color(fn (string $state): string => match ($state) {
                        '🔒 Locked' => 'warning',
                        '✓ Complete' => 'success',
                        default => 'danger',
                    }),
            ])
            ->filters([
                SelectFilter::make('group')
                    ->label(__('Group Filter'))
                    ->options(fn (): array => TranslationKey::distinct()->pluck('group', 'group')->filter()->toArray()),

                SelectFilter::make('status')
                    ->label(__('Status Filter'))
                    ->options([
                        'missing' => __('Missing Translations Only'),
                        'locked' => __('Locked Items Only 🔒'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (($data['value'] ?? null) === 'missing') {
                            return $query->where(function ($q) {
                                $q->whereDoesntHave('values', fn ($vq) => $vq->where('locale', 'ar')->whereNotNull('value')->where('value', '!=', ''))
                                  ->orWhereDoesntHave('values', fn ($vq) => $vq->where('locale', 'en')->whereNotNull('value')->where('value', '!=', ''));
                            });
                        }
                        if (($data['value'] ?? null) === 'locked') {
                            return $query->whereHas('values', fn ($vq) => $vq->where('is_locked', true));
                        }
                        return $query;
                    }),
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('edit')
                        ->label(__('Edit Translation'))
                        ->icon(Heroicon::OutlinedPencilSquare)
                        ->color('primary')
                        ->fillForm(fn (TranslationKey $record): array => [
                            'key_name' => $record->key,
                            'value_ar' => $record->getValueForLocale('ar') ?? '',
                            'value_en' => $record->getValueForLocale('en') ?? '',
                            'is_locked' => (bool) ($record->getTranslationValueModel('ar')?->is_locked || $record->getTranslationValueModel('en')?->is_locked),
                        ])
                        ->form([
                            Textarea::make('value_ar')
                                ->label(__('Arabic Translation (العربية)'))
                                ->rows(3)
                                ->extraAttributes(['dir' => 'rtl']),

                            Textarea::make('value_en')
                                ->label(__('English Translation (English)'))
                                ->rows(3),

                            Toggle::make('is_locked')
                                ->label(__('🔒 Lock Translation (Protect from auto-overwrite)')),
                        ])
                        ->action(function (array $data, TranslationKey $record): void {
                            $manager = new TranslationManagerService(new MyMemoryTranslationService());
                            $userId = Auth::id();

                            $manager->updateTranslation($record->key, 'ar', $data['value_ar'], 'manual', $userId, $data['is_locked']);
                            $manager->updateTranslation($record->key, 'en', $data['value_en'], 'manual', $userId, $data['is_locked']);

                            Notification::make()
                                ->title(__('Translation Saved Successfully!'))
                                ->success()
                                ->send();
                        }),

                    Action::make('auto_translate_ar_to_en')
                        ->label(__('🤖 Translate AR → EN'))
                        ->icon(Heroicon::OutlinedSparkles)
                        ->color('warning')
                        ->action(function (TranslationKey $record) {
                            $manager = new TranslationManagerService(new MyMemoryTranslationService());
                            $manager->translateKey($record, 'ar', 'en', Auth::id());

                            Notification::make()
                                ->title(__('Translated Arabic to English Successfully!'))
                                ->success()
                                ->send();
                        }),

                    Action::make('auto_translate_en_to_ar')
                        ->label(__('🤖 Translate EN → AR'))
                        ->icon(Heroicon::OutlinedSparkles)
                        ->color('warning')
                        ->action(function (TranslationKey $record) {
                            $manager = new TranslationManagerService(new MyMemoryTranslationService());
                            $manager->translateKey($record, 'en', 'ar', Auth::id());

                            Notification::make()
                                ->title(__('Translated English to Arabic Successfully!'))
                                ->success()
                                ->send();
                        }),

                    Action::make('toggle_lock')
                        ->label(fn (TranslationKey $record): string => ($record->getTranslationValueModel('ar')?->is_locked || $record->getTranslationValueModel('en')?->is_locked) ? __('🔓 Unlock Item') : __('🔒 Lock Item'))
                        ->icon(Heroicon::OutlinedLockClosed)
                        ->color('gray')
                        ->action(function (TranslationKey $record) {
                            $arModel = $record->getTranslationValueModel('ar');
                            $enModel = $record->getTranslationValueModel('en');
                            $newState = !($arModel?->is_locked || $enModel?->is_locked);

                            if ($arModel) { $arModel->is_locked = $newState; $arModel->save(); }
                            if ($enModel) { $enModel->is_locked = $newState; $enModel->save(); }

                            Notification::make()
                                ->title($newState ? __('Translation Locked 🔒') : __('Translation Unlocked 🔓'))
                                ->info()
                                ->send();
                        }),
                ]),
            ]);
    }
}
