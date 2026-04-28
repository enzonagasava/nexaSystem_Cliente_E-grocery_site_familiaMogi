<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Models\EGroceryProduct;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min(max((int) $request->query('per_page', 100), 1), 500);

        $query = EGroceryProduct::query()
            ->from('e_grocery_products as p')
            ->leftJoin('e_grocery_images as i', 'i.external_image_id', '=', 'p.external_image_id')
            ->select([
                'p.id',
                'p.external_sku',
                'p.name',
                'p.category',
                'p.price',
                'p.stock',
                'p.status',
                'p.payload',
                DB::raw('COALESCE(i.url, \'\') as image_url'),
            ])
            ->where('p.status', 'active')
            ->whereNotNull('p.name')
            ->orderBy('p.name');

        $products = $query->paginate($perPage);

        $products->getCollection()->transform(function ($product) {
            $payload = is_array($product->payload) ? $product->payload : [];

            return [
                'id' => $product->id,
                'sku' => $product->external_sku,
                'name' => $product->name,
                'category' => $product->category ?: 'Sem categoria',
                'price' => $product->price !== null ? (float) $product->price : 0.0,
                'stock' => (int) ($product->stock ?? 0),
                'status' => $product->status,
                'unit' => $payload['unit'] ?? 'unidade',
                'badge' => $payload['badge'] ?? 'Disponível',
                'shortDescription' => $payload['shortDescription'] ?? ($payload['short_description'] ?? 'Produto sincronizado do painel E-grocery.'),
                'description' => $payload['description'] ?? 'Produto sincronizado automaticamente pelo catálogo integrado.',
                'images' => $this->resolveImages($payload, $product->image_url),
            ];
        });

        return response()->json($products);
    }

    private function resolveImages(array $payload, string $imageUrl): array
    {
        if (isset($payload['images']) && is_array($payload['images']) && $payload['images'] !== []) {
            return array_values(array_filter($payload['images'], fn ($url) => is_string($url) && trim($url) !== ''));
        }

        if (trim($imageUrl) !== '') {
            return [$imageUrl];
        }

        return ['/images/logo-familia-mogi.svg'];
    }
}

