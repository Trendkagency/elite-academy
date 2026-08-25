<?php

namespace App\Jobs;

use App\Services\Translation\MyMemoryTranslationService;
use App\Services\Translation\TranslationManagerService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class TranslateTranslationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 300;

    public string $mode;
    public string $from;
    public string $to;
    public ?int $userId;
    public array $selectedIds;

    public function __construct(string $mode = 'missing', string $from = 'ar', string $to = 'en', ?int $userId = null, array $selectedIds = [])
    {
        $this->mode = $mode;
        $this->from = $from;
        $this->to = $to;
        $this->userId = $userId;
        $this->selectedIds = $selectedIds;
    }

    public function handle(): void
    {
        $service = new TranslationManagerService(new MyMemoryTranslationService());

        Log::info("Starting batch translation job. Mode: {$this->mode}, From: {$this->from}, To: {$this->to}");

        if ($this->mode === 'missing') {
            $translatedCount = $service->batchTranslateMissing($this->from, $this->to, $this->userId);
            Log::info("Batch translation completed. Total translated: {$translatedCount}");
        }
    }
}
