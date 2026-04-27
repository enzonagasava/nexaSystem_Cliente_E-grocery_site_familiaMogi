<?php

namespace App\Services\Integrations;

use App\Models\EGroceryAd;
use App\Models\EGroceryImage;
use App\Models\EGroceryProduct;
use Carbon\CarbonImmutable;

class EGroceryCatalogSyncService
{
    public function __construct(private readonly EGroceryApiClient $apiClient)
    {
    }

    public function sync(?string $updatedSince = null, bool $syncImages = true, int $maxPages = 0): array
    {
        $stats = [
            'ads_synced' => 0,
            'products_synced' => 0,
            'images_synced' => 0,
            'pages_ads' => 0,
            'pages_products' => 0,
        ];

        $stats['ads_synced'] = $this->syncAds($updatedSince, $maxPages, $stats);
        $stats['products_synced'] = $this->syncProducts($updatedSince, $syncImages, $maxPages, $stats);

        return $stats;
    }

    public function upsertAd(array $data): void
    {
        $externalId = (string) ($data['id'] ?? $data['ad_id'] ?? '');
        if ($externalId === '') {
            return;
        }

        EGroceryAd::query()->updateOrCreate(
            ['external_ad_id' => $externalId],
            [
                'title' => $data['title'] ?? null,
                'description' => $data['description'] ?? null,
                'status' => $data['status'] ?? null,
                'priority' => $this->asIntOrNull($data['priority'] ?? null),
                'starts_at' => $this->asDateTimeOrNull($data['starts_at'] ?? null),
                'ends_at' => $this->asDateTimeOrNull($data['ends_at'] ?? null),
                'source_updated_at' => $this->asDateTimeOrNull($data['updated_at'] ?? null),
                'payload' => $data,
            ]
        );
    }

    public function upsertProduct(array $data): void
    {
        $sku = (string) ($data['sku'] ?? '');
        if ($sku === '') {
            return;
        }

        EGroceryProduct::query()->updateOrCreate(
            ['external_sku' => $sku],
            [
                'name' => $data['name'] ?? null,
                'category' => $data['category'] ?? null,
                'price' => $this->asFloatOrNull($data['price'] ?? null),
                'stock' => $this->asIntOrNull($data['stock'] ?? null),
                'status' => $data['status'] ?? null,
                'external_image_id' => $data['image_id'] ?? null,
                'source_updated_at' => $this->asDateTimeOrNull($data['updated_at'] ?? null),
                'payload' => $data,
            ]
        );
    }

    public function upsertImage(array $data, ?string $fallbackExternalImageId = null): void
    {
        $externalImageId = (string) ($data['id'] ?? $data['image_id'] ?? $fallbackExternalImageId ?? '');
        if ($externalImageId === '') {
            return;
        }

        EGroceryImage::query()->updateOrCreate(
            ['external_image_id' => $externalImageId],
            [
                'storage_key' => $data['storage_key'] ?? null,
                'url' => $data['url'] ?? null,
                'mime_type' => $data['mime_type'] ?? null,
                'width' => $this->asIntOrNull($data['width'] ?? null),
                'height' => $this->asIntOrNull($data['height'] ?? null),
                'checksum' => $data['checksum'] ?? null,
                'source_updated_at' => $this->asDateTimeOrNull($data['updated_at'] ?? null),
                'payload' => $data,
            ]
        );
    }

    private function syncAds(?string $updatedSince, int $maxPages, array &$stats): int
    {
        $synced = 0;
        $cursor = null;

        do {
            $response = $this->apiClient->fetchAds($updatedSince, $cursor);
            $stats['pages_ads']++;

            foreach (($response['data'] ?? []) as $ad) {
                if (!is_array($ad)) {
                    continue;
                }

                $this->upsertAd($ad);
                $synced++;
            }

            $cursor = $response['meta']['next_cursor'] ?? null;
            if (!is_string($cursor) || $cursor === '') {
                $cursor = null;
            }
        } while ($cursor !== null && ($maxPages <= 0 || $stats['pages_ads'] < $maxPages));

        return $synced;
    }

    private function syncProducts(?string $updatedSince, bool $syncImages, int $maxPages, array &$stats): int
    {
        $synced = 0;
        $cursor = null;
        $syncedImageIds = [];

        do {
            $response = $this->apiClient->fetchProducts($updatedSince, $cursor);
            $stats['pages_products']++;

            foreach (($response['data'] ?? []) as $product) {
                if (!is_array($product)) {
                    continue;
                }

                $this->upsertProduct($product);
                $synced++;

                if (!$syncImages) {
                    continue;
                }

                $imageId = (string) ($product['image_id'] ?? '');
                if ($imageId === '' || isset($syncedImageIds[$imageId])) {
                    continue;
                }

                $image = $this->apiClient->fetchImage($imageId);
                $this->upsertImage($image, $imageId);
                $syncedImageIds[$imageId] = true;
                $stats['images_synced']++;
            }

            $cursor = $response['meta']['next_cursor'] ?? null;
            if (!is_string($cursor) || $cursor === '') {
                $cursor = null;
            }
        } while ($cursor !== null && ($maxPages <= 0 || $stats['pages_products'] < $maxPages));

        return $synced;
    }

    private function asDateTimeOrNull(mixed $value): ?CarbonImmutable
    {
        if (!is_string($value) || trim($value) === '') {
            return null;
        }

        try {
            return CarbonImmutable::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }

    private function asFloatOrNull(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        return is_numeric($value) ? (float) $value : null;
    }

    private function asIntOrNull(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return is_numeric($value) ? (int) $value : null;
    }
}

