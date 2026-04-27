<?php

namespace App\Http\Controllers\Integrations;

use App\Http\Controllers\Controller;
use App\Models\EGroceryOrderExport;
use App\Services\Integrations\EGroceryOrderExportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EGroceryOrderExportController extends Controller
{
    public function __invoke(Request $request, EGroceryOrderExportService $orderExportService): JsonResponse
    {
        $payload = $request->json()->all();

        if (!is_array($payload) || $payload === []) {
            return response()->json([
                'message' => 'Payload JSON invalido.',
                'code' => 'invalid_json_payload',
            ], 422);
        }

        $validator = Validator::make($payload, [
            'external_order_id' => ['nullable', 'string', 'max:255'],
            'customer' => ['required', 'array'],
            'customer.name' => ['required', 'string', 'max:255'],
            'customer.phone' => ['required', 'string', 'max:60'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.sku' => ['nullable', 'string', 'max:120'],
            'items.*.product_id' => ['nullable'],
            'items.*.qty' => ['nullable', 'integer', 'min:1'],
            'items.*.quantity' => ['nullable', 'integer', 'min:1'],
            'items.*.unit_price' => ['nullable', 'numeric', 'min:0'],
            'items.*.name' => ['nullable', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Payload invalido para exportacao de pedido.',
                'code' => 'invalid_order_export_payload',
                'errors' => $validator->errors(),
            ], 422);
        }

        $result = $orderExportService->queueOrderExport($payload);
        /** @var EGroceryOrderExport $record */
        $record = $result['record'];

        if ($result['duplicate']) {
            return response()->json([
                'message' => 'Pedido ja registrado anteriormente.',
                'status' => $record->status,
                'external_order_id' => $record->external_order_id,
                'panel_order_id' => $record->panel_order_id,
                'attempt_count' => (int) $record->attempt_count,
            ], 200);
        }

        return response()->json([
            'message' => 'Pedido aceito para exportacao assincrona.',
            'status' => 'queued',
            'external_order_id' => $record->external_order_id,
        ], 202);
    }
}
