<?php

namespace App\Jobs;

use App\Models\EGroceryOrderExport;
use App\Services\Integrations\EGroceryApiClient;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendEGroceryOrderJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 4;

    public array $backoff = [60, 300, 900, 3600];

    public function __construct(public int $orderExportId)
    {
    }

    public function handle(EGroceryApiClient $apiClient): void
    {
        $orderExport = EGroceryOrderExport::query()->find($this->orderExportId);

        if (!$orderExport) {
            Log::channel('e_grocery_integration')->warning('Order export record missing for queued job', [
                'order_export_id' => $this->orderExportId,
            ]);

            return;
        }

        if ($orderExport->status === 'sent') {
            return;
        }

        $orderExport->forceFill([
            'status' => 'processing',
            'attempt_count' => (int) $orderExport->attempt_count + 1,
            'last_attempt_at' => now(),
            'error_message' => null,
        ])->save();

        $response = $apiClient->createOrder((array) $orderExport->normalized_payload);

        $orderExport->forceFill([
            'status' => 'sent',
            'panel_response' => $response,
            'panel_order_id' => $response['order_id'] ?? null,
            'exported_at' => now(),
            'error_message' => null,
        ])->save();

        Log::channel('e_grocery_integration')->info('Order exported to e-grocery', [
            'order_export_id' => $orderExport->id,
            'external_order_id' => $orderExport->external_order_id,
            'panel_order_id' => $orderExport->panel_order_id,
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        $orderExport = EGroceryOrderExport::query()->find($this->orderExportId);

        if ($orderExport) {
            $orderExport->forceFill([
                'status' => 'failed',
                'error_message' => mb_substr($exception->getMessage(), 0, 1000),
            ])->save();
        }

        Log::channel('e_grocery_integration')->error('Failed to export order to e-grocery', [
            'order_export_id' => $this->orderExportId,
            'error' => $exception->getMessage(),
        ]);
    }
}

