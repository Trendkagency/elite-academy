<x-filament-panels::page>
    <div x-data="{ viewMode: 'desktop' }" class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-start">
        {{-- Left Side: Contact CMS Edit Form --}}
        <div class="xl:col-span-5 space-y-6">
            <form wire:submit="save" class="space-y-6">
                {{ $this->form }}

                <x-filament::button type="submit" class="w-full">
                    Save Contact Page Content Settings
                </x-filament::button>
            </form>
        </div>

        {{-- Right Side: iFrame Live UI Preview of Contact Page --}}
        <div class="xl:col-span-7 space-y-4">
            <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-3">
                <div>
                    <h3 class="font-bold text-sm text-slate-900 dark:text-slate-100 flex items-center gap-2">
                        <span>Live Contact UI Preview</span>
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Real-time view of the contact page layout</p>
                </div>

                {{-- Device Viewport Mode Toggles --}}
                <div class="flex items-center gap-1 bg-slate-100 dark:bg-slate-800 p-1 rounded-xl border border-slate-200 dark:border-slate-700">
                    <button type="button" @click.prevent="viewMode = 'desktop'" :class="viewMode === 'desktop' ? 'bg-white dark:bg-slate-700 text-teal-600 dark:text-teal-400 shadow-xs font-bold' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900'" class="px-3 py-1 text-xs rounded-lg transition-all cursor-pointer">
                        Desktop
                    </button>
                    <button type="button" @click.prevent="viewMode = 'tablet'" :class="viewMode === 'tablet' ? 'bg-white dark:bg-slate-700 text-teal-600 dark:text-teal-400 shadow-xs font-bold' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900'" class="px-3 py-1 text-xs rounded-lg transition-all cursor-pointer">
                        Tablet
                    </button>
                    <button type="button" @click.prevent="viewMode = 'mobile'" :class="viewMode === 'mobile' ? 'bg-white dark:bg-slate-700 text-teal-600 dark:text-teal-400 shadow-xs font-bold' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900'" class="px-3 py-1 text-xs rounded-lg transition-all cursor-pointer">
                        Mobile
                    </button>
                </div>
            </div>

            {{-- iFrame Preview Container --}}
            <div class="p-4 bg-slate-200/60 dark:bg-slate-800/60 rounded-2xl border border-slate-300 dark:border-slate-700 shadow-inner flex justify-center items-center overflow-hidden min-h-[750px]">
                <div :class="{
                    'w-full h-[720px] border-2 border-slate-300': viewMode === 'desktop',
                    'w-[768px] max-w-full h-[720px] mx-auto border-4 border-slate-600': viewMode === 'tablet',
                    'w-[375px] max-w-full h-[720px] mx-auto border-8 border-slate-900 rounded-[2rem]': viewMode === 'mobile'
                }" class="transition-all duration-300 bg-white rounded-2xl shadow-2xl overflow-hidden">
                    <iframe id="contact-preview-iframe" src="{{ url('/contact?iframe=1') }}" title="Contact Page UI Preview" class="w-full h-full border-0 block"></iframe>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
