<?php

namespace App\Services\Integrations;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class EGroceryApiClient
{
    public function fetchAds(?string $updatedSince = null, ?string $cursor = null): array
    {
        $query = [];

        if ($updatedSince) {
            $query['updated_since'] = $updatedSince;
        }

        if ($cursor) {
            $query['cursor'] = $cursor;
        }

        return $this->get('/api/v1/anuncios', $query);
    }

    public function fetchProducts(?string $updatedSince = null, ?string $cursor = null): array
    {
        $query = [];

        if ($updatedSince) {
            $query['updated_since'] = $updatedSince;
        }

        if ($cursor) {
            $query['cursor'] = $cursor;
        }

        return $this->get('/api/v1/produtos', $query);
    }

    public function fetchImage(string $imageId): array
    {
        return $this->get('/api/v1/imagens/'.urlencode($imageId));
    }

    public function createOrder(array $payload): array
    {
        $response = $this->request()->post('api/v1/pedidos', $payload);

        if (!$response->successful()) {
            throw new RuntimeException(sprintf(
                'EGrocery API POST /api/v1/pedidos failed with status %d',
                $response->status()
            ));
        }

        $responsePayload = $response->json();

        if (!is_array($responsePayload)) {
            throw new RuntimeException('EGrocery API returned non-array JSON payload for order creation.');
        }

        return $responsePayload;
    }

    private function get(string $path, array $query = []): array
    {
        $response = $this->request()->get(ltrim($path, '/'), $query);

        if (!$response->successful()) {
            throw new RuntimeException(sprintf(
                'EGrocery API GET %s failed with status %d',
                $path,
                $response->status()
            ));
        }

        $payload = $response->json();

        if (!is_array($payload)) {
            throw new RuntimeException('EGrocery API returned non-array JSON payload.');
        }

        return $payload;
    }

    private function request(): PendingRequest
    {
        $baseUrl = (string) config('integrations.e_grocery.base_url', '');
        $timeout = (int) config('integrations.e_grocery.timeout_seconds', 10);
        $token = (string) config('integrations.e_grocery.api_token');

        if ($baseUrl === '') {
            throw new RuntimeException('EGROCERY_API_BASE_URL is not configured.');
        }

        $request = Http::acceptJson()
            ->timeout($timeout)
            ->baseUrl($baseUrl);

        if ($token !== '') {
            $request = $request->withToken($token);
        }

        return $request;
    }
}
