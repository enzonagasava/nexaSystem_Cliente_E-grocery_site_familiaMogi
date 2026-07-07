<?php

namespace App\Jobs;

use App\Models\IntegrationEvent;
use App\Services\Integrations\EGroceryWebhookEventHandler;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Throwable;

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
        Log::channel('e_grocery_integration')->info('[ProcessEGroceryWebhookEventJob] Iniciando job', [
            'integration_event_id' => $this->integrationEventId,
        ]);

        $event = IntegrationEvent::query()->find($this->integrationEventId);

        if (!$event) {
            Log::channel('e_grocery_integration')->warning('[ProcessEGroceryWebhookEventJob] Evento não encontrado', [
                'integration_event_id' => $this->integrationEventId,
            ]);
            return;
        }

        if ($event->status === 'processed') {
            Log::info('[ProcessEGroceryWebhookEventJob] Evento já processado, ignorando', [
                'event_id' => $event->event_id,
                'status' => $event->status,
            ]);
            return;
        }

        Log::channel('e_grocery_integration')->info('[ProcessEGroceryWebhookEventJob] Evento encontrado', [
            'integration_event_id' => $event->id,
            'event_id' => $event->event_id,
            'event_type' => $event->event_type,
            'status_atual' => $event->status,
        ]);

        // Marca como processing
        $event->forceFill(['status' => 'processing'])->save();

        Log::channel('e_grocery_integration')->info('[ProcessEGroceryWebhookEventJob] Status atualizado para processing');

        try {
            // Executa o handler (que pode lançar exceções)
            $handler->handle($event);

            // Se chegou aqui, foi bem-sucedido
            $event->forceFill([
                'status' => 'processed',
                'processed_at' => now(),
                'error_message' => null,
            ])->save();

            Log::channel('e_grocery_integration')->info('[ProcessEGroceryWebhookEventJob] Evento processado com sucesso', [
                'event_id' => $event->event_id,
                'event_type' => $event->event_type,
            ]);

        } catch (Throwable $e) {
            Log::channel('e_grocery_integration')->error('[ProcessEGroceryWebhookEventJob] Exceção durante processamento', [
                'integration_event_id' => $this->integrationEventId,
                'event_id' => $event->event_id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Atualiza o evento com erro (opcional, mas mantemos para rastreio)
            $event->forceFill([
                'status' => 'failed',
                'error_message' => mb_substr($e->getMessage(), 0, 1000),
                'processed_at' => null,
            ])->save();

            // Relança para que o Laravel chame o método failed()
            throw $e;
        }
    }

    public function failed(Throwable $exception): void
    {
        Log::channel('e_grocery_integration')->error('[ProcessEGroceryWebhookEventJob] Job falhou permanentemente', [
            'integration_event_id' => $this->integrationEventId,
            'error' => $exception->getMessage(),
        ]);

        $event = IntegrationEvent::query()->find($this->integrationEventId);

        if (!$event) {
            Log::channel('e_grocery_integration')->warning('[ProcessEGroceryWebhookEventJob] Evento não encontrado no failed()', [
                'integration_event_id' => $this->integrationEventId,
            ]);
            return;
        }

        // Atualiza status para failed (caso não tenha sido feito no handle)
        $event->forceFill([
            'status' => 'failed',
            'error_message' => mb_substr($exception->getMessage(), 0, 1000),
            'processed_at' => null,
        ])->save();

        Log::channel('e_grocery_integration')->info('[ProcessEGroceryWebhookEventJob] Status atualizado para failed', [
            'event_id' => $event->event_id,
            'error' => $event->error_message,
        ]);
    }
}