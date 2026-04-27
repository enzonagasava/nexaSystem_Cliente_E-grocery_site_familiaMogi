<?php

namespace App\Http\Controllers\Integrations;

use App\Http\Controllers\Controller;
use App\Models\EGroceryImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class EGroceryImageStorageController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min(max((int) $request->query('per_page', 20), 1), 100);

        $query = EGroceryImage::query()->orderByDesc('id');

        $externalImageId = (string) $request->query('external_image_id', '');
        if ($externalImageId !== '') {
            $query->where('external_image_id', $externalImageId);
        }

        $items = $query->paginate($perPage);

        return response()->json($items);
    }

    public function show(string $externalImageId): JsonResponse
    {
        $image = EGroceryImage::query()->where('external_image_id', $externalImageId)->first();

        if (!$image) {
            return response()->json([
                'message' => 'Imagem nao encontrada.',
                'code' => 'image_not_found',
            ], 404);
        }

        return response()->json($image);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'image' => ['required', 'file', 'image', 'max:8192'],
            'external_image_id' => ['nullable', 'string', 'max:255'],
            'product_sku' => ['nullable', 'string', 'max:120'],
            'folder' => ['nullable', 'string', 'max:120'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Payload invalido para upload de imagem.',
                'code' => 'invalid_image_upload_payload',
                'errors' => $validator->errors(),
            ], 422);
        }

        $file = $request->file('image');
        if (!$file) {
            return response()->json([
                'message' => 'Arquivo de imagem ausente.',
                'code' => 'image_file_missing',
            ], 422);
        }

        $externalImageId = (string) $request->input('external_image_id', '');
        if ($externalImageId === '') {
            $externalImageId = 'img_'.Str::lower(Str::random(12));
        }

        $folder = trim((string) $request->input('folder', 'catalog'));
        $folder = trim($folder, '/');
        if ($folder === '') {
            $folder = 'catalog';
        }

        $sku = trim((string) $request->input('product_sku', 'misc'));
        $safeSku = Str::slug($sku, '-');
        if ($safeSku === '') {
            $safeSku = 'misc';
        }

        $extension = strtolower((string) $file->getClientOriginalExtension());
        if ($extension === '') {
            $extension = 'jpg';
        }

        $filename = $externalImageId.'.'.$extension;
        $storageKey = sprintf(
            '%s/%s/%s/%s',
            $folder,
            now()->format('Y/m/d'),
            $safeSku,
            $filename
        );

        Storage::disk('s3')->put(
            $storageKey,
            file_get_contents($file->getRealPath()),
            [
                'visibility' => 'public',
                'ContentType' => $file->getMimeType() ?: 'application/octet-stream',
            ]
        );

        $width = null;
        $height = null;
        $size = @getimagesize($file->getRealPath());
        if (is_array($size)) {
            $width = $size[0] ?? null;
            $height = $size[1] ?? null;
        }

        $checksum = hash_file('sha256', $file->getRealPath());
        $url = Storage::disk('s3')->url($storageKey);
        $mimeType = $file->getMimeType() ?: null;

        $model = EGroceryImage::query()->updateOrCreate(
            ['external_image_id' => $externalImageId],
            [
                'storage_key' => $storageKey,
                'url' => $url,
                'mime_type' => $mimeType,
                'width' => is_numeric($width) ? (int) $width : null,
                'height' => is_numeric($height) ? (int) $height : null,
                'checksum' => $checksum ?: null,
                'source_updated_at' => now(),
                'payload' => [
                    'external_image_id' => $externalImageId,
                    'storage_key' => $storageKey,
                    'url' => $url,
                    'mime_type' => $mimeType,
                    'width' => $width,
                    'height' => $height,
                    'checksum' => $checksum,
                    'source' => 'familiaMogi-upload',
                ],
            ]
        );

        return response()->json([
            'message' => 'Imagem enviada para storage externo com sucesso.',
            'image' => $model,
        ], 201);
    }
}

