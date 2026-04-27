<?php

namespace App\Services\Integrations;

use App\Jobs\SendEGroceryOrderJob;
use App\Models\EGroceryOrderExport;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class EGroceryOrderExportService
{
    public function queueOrderExport(array $payload, string $source = 'familiaMogi-api'): array
    {
        $externalOrderId = $this->resolveExternalOrderId($payload);
        $normalizedPayload = $this->normalizePayload($payload, $externalOrderId);

        $orderExport = EGroceryOrderExport::query()->firstOrCreate(
            ['external_order_id' => $externalOrderId],
            [
                'source' => $source,
                'status' => 'queued',
                'request_payload' => $payload,
                'normalized_payload' => $normalizedPayload,
                'attempt_count' => 0,
            ]
        );

        if (!$orderExport->wasRecentlyCreated) {
            Log::channel('e_grocery_integration')->info('Duplicate order export request', [
                'order_export_id' => $orderExport->id,
                'external_order_id' => $externalOrderId,
                'status' => $orderExport->status,
            ]);

            return [
                'record' => $orderExport,
                'queued_now' => false,
                'duplicate' => true,
            ];
        }

        SendEGroceryOrderJob::dispatch($orderExport->id);

        Log::channel('e_grocery_integration')->info('Order export queued', [
            'order_export_id' => $orderExport->id,
            'external_order_id' => $externalOrderId,
        ]);

        return [
            'record' => $orderExport,
            'queued_now' => true,
            'duplicate' => false,
        ];
    }

    private function resolveExternalOrderId(array $payload): string
    {
        $provided = (string) ($payload['external_order_id'] ?? '');

        if (trim($provided) !== '') {
            return trim($provided);
        }

        return sprintf('fm-%s-%s', now()->format('YmdHis'), Str::lower(Str::random(6)));
    }

    private function normalizePayload(array $payload, string $externalOrderId): array
    {
        $createdAt = $this->resolveCreatedAt($payload);
        $items = $this->normalizeItems((array) ($payload['items'] ?? []));
        $subtotal = $this->asFloat($payload['totals']['subtotal'] ?? $payload['subtotal'] ?? 0);
        $deliveryFee = $this->asFloat($payload['totals']['delivery_fee'] ?? $payload['shipping'] ?? 0);
        $discount = $this->asFloat($payload['totals']['discount'] ?? 0);
        $grandTotal = $this->asFloat($payload['totals']['grand_total'] ?? $payload['total'] ?? ($subtotal + $deliveryFee - $discount));

        $deliveryInput = is_array($payload['delivery'] ?? null) ? $payload['delivery'] : [];
        $addressInput = is_array($deliveryInput['address'] ?? null) ? $deliveryInput['address'] : [];

        $address = [
            'zip' => $addressInput['zip'] ?? $deliveryInput['cep'] ?? null,
            'street' => $addressInput['street'] ?? $deliveryInput['address'] ?? null,
            'number' => $addressInput['number'] ?? $deliveryInput['address_number'] ?? null,
            'neighborhood' => $addressInput['neighborhood'] ?? $deliveryInput['neighborhood'] ?? null,
            'city' => $addressInput['city'] ?? $deliveryInput['city'] ?? null,
            'state' => $addressInput['state'] ?? $this->extractStateFromCity((string) ($deliveryInput['city'] ?? '')) ?? null,
            'notes' => $deliveryInput['notes'] ?? null,
        ];

        return [
            'external_order_id' => $externalOrderId,
            'created_at' => $createdAt,
            'customer' => [
                'name' => $payload['customer']['name'] ?? null,
                'phone' => $payload['customer']['phone'] ?? null,
                'email' => $payload['customer']['email'] ?? null,
            ],
            'items' => $items,
            'totals' => [
                'subtotal' => $subtotal,
                'delivery_fee' => $deliveryFee,
                'discount' => $discount,
                'grand_total' => $grandTotal,
            ],
            'payment' => [
                'method' => $payload['payment']['method'] ?? $payload['payment_method'] ?? null,
                'status' => $payload['payment']['status'] ?? 'pending',
            ],
            'delivery' => [
                'type' => $deliveryInput['type'] ?? 'delivery',
                'address' => $address,
            ],
            'source' => $payload['source'] ?? 'familiaMogi',
        ];
    }

    /**
     * @param array<int, mixed> $items
     * @return array<int, array<string, mixed>>
     */
    private function normalizeItems(array $items): array
    {
        $normalized = [];

        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }

            $sku = $item['sku'] ?? $item['product_id'] ?? null;
            $qty = $item['qty'] ?? $item['quantity'] ?? 1;
            $unitPrice = $item['unit_price'] ?? null;

            $normalized[] = [
                'sku' => (string) $sku,
                'name' => $item['name'] ?? null,
                'qty' => (int) $qty,
                'unit_price' => $this->asFloat($unitPrice),
            ];
        }

        return $normalized;
    }

    private function resolveCreatedAt(array $payload): string
    {
        $value = $payload['created_at'] ?? null;

        if (is_string($value) && trim($value) !== '') {
            try {
                return CarbonImmutable::parse($value)->toIso8601String();
            } catch (\Throwable) {
                // fall through
            }
        }

        return now()->toIso8601String();
    }

    private function asFloat(mixed $value): float
    {
        if ($value === null || $value === '') {
            return 0.0;
        }

        return is_numeric($value) ? (float) $value : 0.0;
    }

    private function extractStateFromCity(string $city): ?string
    {
        if (!str_contains($city, '-')) {
            return null;
        }

        $parts = array_map('trim', explode('-', $city));
        $last = end($parts);

        if (!is_string($last) || strlen($last) < 2 || strlen($last) > 3) {
            return null;
        }

        return strtoupper($last);
    }
}

