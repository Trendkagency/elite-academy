<x-filament-panels::page>
    <div class="max-w-4xl space-y-6">
        <div class="p-6 bg-slate-900 text-white rounded-3xl border border-teal-500/40 shadow-xl space-y-2">
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-teal-400 animate-ping"></span>
                <h2 class="font-bold text-lg text-teal-300">Firebase Cloud Messaging (FCM) & Web Push Broadcaster</h2>
            </div>
            <p class="text-xs text-slate-300 font-mono leading-relaxed">
                Compose customized push notifications and send them directly to specific target audience groups (Students, Teachers, Parents, or All Users). Each user receives an instant Web Push notification and a system notification entry in their portal feed.
            </p>
        </div>

        <form wire:submit="sendBroadcast" class="space-y-6">
            {{ $this->form }}

            <x-filament::button type="submit" size="lg" color="primary" icon="heroicon-o-paper-airplane" class="w-full sm:w-auto">
                Dispatch FCM Push Broadcast Now
            </x-filament::button>
        </form>
    </div>
</x-filament-panels::page>
