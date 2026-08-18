<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\EGroceryAd;
use App\Models\EGroceryImage;
use Illuminate\Support\Str;


class ProductsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min(max((int) $request->query('per_page', 100), 1), 500);


        $query = EGroceryAd::query()
        ->from('e_grocery_ads as p')
        ->select([
            'p.id',
            'p.title',
            'p.description',
            'p.status',
            'p.payload',
            'p.category',
        ])
        ->where('p.status', 'active')
        ->whereNotNull('p.title')
        ->orderBy('p.title');

        $products = $query->paginate($perPage);

        $products->getCollection()->transform(function ($product) {
            $payload = is_array($product->payload) ? $product->payload : [];

            return [
                'id' => $product->id,
                'sku' => $product->external_sku ,
                'name' => $product->title,
                'category' => $product->category ?: 'Sem categoria',
                'price' => $product->price !== null ? (float) $product->price : 0.0,
                'stock' => (int) ($product->stock ?? 0),
                'status' => $product->status,
                'unit' => $payload['unit'] ?? 'unidade',
                'badge' => $payload['badge'] ?? 'Disponível',
                'shortDescription' => Str::words($payload['shortDescription'] ?? $product->description ?? '',15,'...'),
                'description' => $product->description ?? 'Produto sincronizado automaticamente pelo catálogo integrado.',
                'images' => $this->resolveImages($product->id),
            ];
        });

        return response()->json($products);
    }

    private function resolveImages(int $productId)
    {
        if(isset($productId)){
              $product = EGroceryAd::findOrFail($productId);

                return $product->images
                    ->pluck('url')
                    ->values()
                    ->toArray();
        } else {
            return ['/images/logo-familia-mogi.svg'];
        }

    }
}

