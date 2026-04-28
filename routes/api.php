<?php

use App\Http\Controllers\Catalog\ProductsController;
use App\Http\Controllers\Integrations\EGroceryOrderExportController;
use App\Http\Controllers\Integrations\EGroceryImageStorageController;
use App\Http\Controllers\Integrations\EGroceryIntegrationHealthController;
use App\Http\Controllers\Integrations\EGroceryWebhookController;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

Route::get('/cep/{cep}', function (string $cep) {
    $digits = preg_replace('/\D/', '', $cep);

    if (strlen($digits) !== 8) {
        return response()->json([
            'message' => 'CEP inválido. Informe 8 números.',
        ], 422);
    }

    $normalize = function (array $payload) use ($digits): array {
        return [
            'cep' => $payload['cep'] ?? $payload['codigoPostal'] ?? $digits,
            'street' => $payload['logradouro'] ?? $payload['logradouroDNEC'] ?? $payload['street'] ?? $payload['address'] ?? '',
            'neighborhood' => $payload['bairro'] ?? $payload['district'] ?? $payload['neighborhood'] ?? '',
            'city' => $payload['localidade'] ?? $payload['cidade'] ?? $payload['city'] ?? '',
            'state' => $payload['uf'] ?? $payload['estado'] ?? $payload['state'] ?? '',
        ];
    };

    $result = [
        'cep' => $digits,
        'street' => '',
        'neighborhood' => '',
        'city' => '',
        'state' => '',
    ];

    $token = config('services.correios.token');
    $urlTemplate = config('services.correios.cep_url', 'https://api.correios.com.br/cep/v2/ceps/{cep}');
    $correiosUrl = str_replace('{cep}', $digits, $urlTemplate);

    if (!empty($token)) {
        $correiosResponse = Http::acceptJson()
            ->withToken($token)
            ->timeout(10)
            ->get($correiosUrl);

        if ($correiosResponse->successful() && is_array($correiosResponse->json())) {
            $result = array_merge($result, $normalize($correiosResponse->json()));
        }
    }

    $missingMainFields = empty($result['street']) || empty($result['neighborhood']);

    if ($missingMainFields) {
        $viaCepResponse = Http::acceptJson()
            ->timeout(10)
            ->get("https://viacep.com.br/ws/{$digits}/json/");

        if ($viaCepResponse->successful() && is_array($viaCepResponse->json()) && !($viaCepResponse->json()['erro'] ?? false)) {
            $result = array_merge($result, $normalize($viaCepResponse->json()));
        }
    }

    if (empty($result['street']) && empty($result['neighborhood']) && empty($result['city'])) {
        return response()->json([
            'message' => 'Não foi possível localizar este CEP.',
        ], 404);
    }

    return response()->json($result);
});

Route::get('/v1/catalog/products', [ProductsController::class, 'index']);

Route::prefix('v1/integrations/e-grocery')->group(function () {
    Route::post('/webhooks', EGroceryWebhookController::class);
    Route::post('/orders', EGroceryOrderExportController::class);
    Route::get('/images', [EGroceryImageStorageController::class, 'index']);
    Route::get('/images/{externalImageId}', [EGroceryImageStorageController::class, 'show']);
    Route::post('/images/upload', [EGroceryImageStorageController::class, 'store']);
    Route::get('/health', EGroceryIntegrationHealthController::class);
});
