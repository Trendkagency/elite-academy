<x-filament-panels::page>
    <form wire:submit="save" class="space-y-6">
        {{ $this->form }}

        <div class="flex items-center gap-4 pt-4">
            <x-filament::button type="submit" size="lg" icon="heroicon-o-check">
                Save & Apply Landing Page Settings
            </x-filament::button>

            <a href="{{ route('home') }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs font-bold text-teal-600 hover:text-teal-700 underline">
                <span>Preview Live Landing Page ↗</span>
            </a>
        </div>
    </form>
</x-filament-panels::page>