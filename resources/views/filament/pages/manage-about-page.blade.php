<x-filament-panels::page>
    <div x-data="{ viewMode: 'desktop' }" class="cms-preview-grid">
        {{-- Left Side: About CMS Edit Form --}}
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            <form wire:submit="save" style="display: flex; flex-direction: column; gap: 1.5rem;">
                {{ $this->form }}

                <x-filament::button type="submit" style="width: 100%;">
                    {{ __('Save About Page Content Settings') }}
                </x-filament::button>
            </form>
        </div>

        {{-- Right Side: iFrame Live UI Preview of About Page --}}
        <div style="display: flex; flex-direction: column;">
            <div class="cms-preview-toolbar">
                <div>
                    <h3 class="cms-preview-title">
                        {{ __('Live About UI Preview') }}
                    </h3>
                    <p class="cms-preview-sub">{{ __('Real-time view of the about page layout') }}</p>
                </div>

                {{-- Device Viewport Mode Toggles --}}
                <div class="cms-toggle-group">
                    <button type="button" @click.prevent="viewMode = 'desktop'" :class="viewMode === 'desktop' ? 'is-active' : ''" class="cms-toggle-btn">
                        {{ __('Desktop') }}
                    </button>
                    <button type="button" @click.prevent="viewMode = 'tablet'" :class="viewMode === 'tablet' ? 'is-active' : ''" class="cms-toggle-btn">
                        {{ __('Tablet') }}
                    </button>
                    <button type="button" @click.prevent="viewMode = 'mobile'" :class="viewMode === 'mobile' ? 'is-active' : ''" class="cms-toggle-btn">
                        {{ __('Mobile') }}
                    </button>
                </div>
            </div>

            {{-- iFrame Preview Container --}}
            <div class="cms-preview-stage">
                <div :class="{
                    'cms-frame-desktop': viewMode === 'desktop',
                    'cms-frame-tablet': viewMode === 'tablet',
                    'cms-frame-mobile': viewMode === 'mobile'
                }" class="cms-preview-frame">
                    <iframe id="about-preview-iframe" src="{{ url('/about?iframe=1') }}" title="About Page UI Preview" style="width: 100%; height: 100%; border: 0; display: block;"></iframe>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
