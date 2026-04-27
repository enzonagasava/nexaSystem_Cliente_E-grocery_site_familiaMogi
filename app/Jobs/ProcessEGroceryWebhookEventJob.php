<?php

namespace App\Jobs;

use App\Models\IntegrationEvent;
use App\Services\Integrations\EGroceryWebhookEventHandler;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProcessEGroceryWebhookEventJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public array $backoff = [60, 300, 900];

    public function __construct(public int $integrationEventId)
    {
    }

    public function handle(EGroceryWebhookEventHandler $handler): void
    {
        $event = IntegrationEvent::query()->find($this->integrationEventId);

        if (!$event) {
            Log::channel('e_grocery_integration')->warning('Integration event missing for processing job', [
                'integration_event_id' => $this->integrationEventId,
            ]);

            return;
        }

        $event->forceFill(['status' => 'processing'])->save();

        Log::channel('e_grocery_integration')->info('Processing queued e-grocery event', [
            'integration_event_id' => $event->id,
            'event_id' => $event->event_id,
            'event_type' => $event->event_type,
        ]);

        $handler->handle($event);

        $event->forceFill([
            'status' => 'processed',
            'processed_at' => now(),
            'error_message' => null,
        ])->save();
    }

    public function failed(\Throwable $exception): void
    {
        $event = IntegrationEvent::query()->find($this->integrationEventId);

        if ($event) {
            $event->forceFill([
                'status' => 'failed',
                'error_message' => mb_substr($exception->getMessage(), 0, 1000),
            ])->save();
        }

        Log::channel('e_grocery_integration')->error('Failed processing e-grocery webhook event', [
            'integration_event_id' => $this->integrationEventId,
            'error' => $exception->getMessage(),
        ]);
    }
}
