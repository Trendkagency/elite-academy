<x-filament-panels::page>
    <div class="max-w-6xl space-y-6">
        <form wire:submit="save" class="space-y-6">
            {{ $this->form }}

            <div class="flex items-center gap-3 pt-2">
                <x-filament::button type="submit" size="lg" icon="heroicon-o-check-circle" color="primary">
                    {{ app()->getLocale() === 'ar' ? 'حفظ كافة إعدادات المنصة والفوتر' : 'Save Platform & Footer Settings' }}
                </x-filament::button>
            </div>
        </form>
    </div>
</x-filament-panels::page>
