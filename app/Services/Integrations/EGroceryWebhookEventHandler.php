<?php

namespace App\Services\Integrations;

use App\Models\EGroceryImage;
use App\Models\IntegrationEvent;
use Illuminate\Support\Facades\Log;

class EGroceryWebhookEventHandler
{
    public function __construct(private readonly EGroceryCatalogSyncService $catalogSync)
    {
    }

    public function handle(IntegrationEvent $event): void
    {
        $eventType = (string) $event->event_type;
        $payload = is_array($event->payload) ? $event->payload : [];
        $data = is_array($payload['data'] ?? null) ? $payload['data'] : [];
        $entity = is_array($payload['entity'] ?? null) ? $payload['entity'] : [];

        switch ($eventType) {
            case 'ad.created':
            case 'ad.updated':
                $this->catalogSync->upsertAd($data);
                return;

            case 'ad.deleted':
                $externalId = (string) ($entity['id'] ?? $data['id'] ?? '');
                if ($externalId !== '') {
                    $this->markAdDeleted($externalId);
                }
                return;

            case 'product.updated':
            case 'price.updated':
            case 'stock.updated':
                $this->catalogSync->upsertProduct($data);
                $this->syncImageFromProductPayload($data);
                return;

            case 'image.updated':
                $fallbackId = (string) ($entity['id'] ?? '');
                $this->catalogSync->upsertImage($data, $fallbackId);
                return;

            case 'image.deleted':
                $imageId = (string) ($entity['id'] ?? $data['id'] ?? '');
                if ($imageId !== '') {
                    EGroceryImage::query()->where('external_image_id', $imageId)->delete();
                }
                return;

            default:
                Log::channel('e_grocery_integration')->info('No-op handler for event type', [
                    'integration_event_id' => $event->id,
                    'event_type' => $eventType,
                ]);
        }
    }

    private function markAdDeleted(string $externalId): void
    {
        $this->catalogSync->upsertAd([
            'id' => $externalId,
            'status' => 'deleted',
            'updated_at' => now()->toIso8601String(),
        ]);
    }

    private function syncImageFromProductPayload(array $data): void
    {
        $imageId = (string) ($data['image_id'] ?? '');
        if ($imageId === '') {
            return;
        }

        $imagePayload = [];

        if (isset($data['image']) && is_array($data['image'])) {
            $imagePayload = $data['image'];
        }

        if ($imagePayload !== []) {
            $this->catalogSync->upsertImage($imagePayload, $imageId);
            return;
        }
}

