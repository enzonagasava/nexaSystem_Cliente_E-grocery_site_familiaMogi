<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Models\EGroceryProduct;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class ProductsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min(max((int) $request->query('per_page', 100), 1), 500);


        $query = EGroceryProduct::query()
        ->from('e_grocery_ads as p')
        ->select([
            'p.id',
            'p.title',
            'p.description',
            'p.status',
            'p.payload',
        ])
        ->where('p.status', 'active')
        ->whereNotNull('p.title')
        ->orderBy('p.title');

        $products = $query->paginate($perPage);

        $products->getCollection()->transform(function ($product) {
            $payload = is_array($product->payload) ? $product->payload : [];

            return [
                'id' => $product->id,
                'sku' => $product->external_sku,
                'name' => $product->title,
                'category' => $product->category ?: 'Sem categoria',
                'price' => $product->price !== null ? (float) $product->price : 0.0,
                'stock' => (int) ($product->stock ?? 0),
                'status' => $product->status,
                'unit' => $payload['unit'] ?? 'unidade',
                'badge' => $payload['badge'] ?? 'Disponível',
                'shortDescription' => $payload['shortDescription'] ?? ($payload['short_description'] ?? 'Produto sincronizado do painel E-grocery.'),
                'description' => $payload['description'] ?? 'Produto sincronizado automaticamente pelo catálogo integrado.',
                'images' => $this->resolveImages($payload, null),
            ];
        });

        return response()->json($products);
    }

    private function resolveImages(array $payload, ?string $imageUrl): array
    {
        // 1. Tenta extrair do payload
        if (isset($payload['images']) && is_array($payload['images']) && $payload['images'] !== []) {
            return array_values(array_filter($payload['images'], fn ($url) => is_string($url) && trim($url) !== ''));
        }

        // 2. Se não houver no payload, usa o $imageUrl (se não for nulo)
        if ($imageUrl !== null && trim($imageUrl) !== '') {
            return [$imageUrl];
        }

        // 3. Fallback padrão
        return ['/images/logo-familia-mogi.svg'];
    }
}

