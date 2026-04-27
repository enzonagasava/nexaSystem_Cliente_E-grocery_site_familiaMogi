<?php

namespace App\Http\Controllers\Integrations;

use App\Jobs\ProcessEGroceryWebhookEventJob;
use App\Http\Controllers\Controller;
use App\Models\IntegrationEvent;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class EGroceryWebhookController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $eventId = $request->header('X-Event-Id');
        $eventType = $request->header('X-Event-Type');
        $eventTime = $request->header('X-Event-Time');
        $signature = $request->header('X-Signature');

        if (!$eventId || !$eventType || !$eventTime || !$signature) {
            return response()->json([
                'message' => 'Cabeçalhos obrigatorios ausentes.',
                'code' => 'missing_webhook_headers',
            ], 422);
        }

        $rawBody = (string) $request->getContent();
        $payload = $request->json()->all();

        if (!is_array($payload) || $payload === []) {
            return response()->json([
                'message' => 'Payload JSON invalido.',
                'code' => 'invalid_json_payload',
            ], 422);
        }

        $validator = Validator::make($payload, [
            'event_id' => ['required', 'string', 'max:255'],
            'event_type' => ['required', 'string', 'max:120'],
            'occurred_at' => ['required', 'date'],
            'data' => ['required', 'array'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Payload invalido para webhook e-grocery.',
                'code' => 'invalid_webhook_schema',
                'errors' => $validator->errors(),
            ], 422);
        }

        if ($payload['event_id'] !== $eventId || $payload['event_type'] !== $eventType) {
            return response()->json([
                'message' => 'Inconsistencia entre header e payload.',
                'code' => 'header_payload_mismatch',
            ], 422);
        }

        $secret = (string) config('integrations.e_grocery.webhook_secret', '');

        if ($secret === '') {
            return response()->json([
                'message' => 'Webhook secret nao configurado.',
                'code' => 'webhook_secret_missing',
            ], 503);
        }

        $expectedSignature = hash_hmac('sha256', $rawBody, $secret);
        if (!hash_equals($expectedSignature, (string) $signature)) {
            return response()->json([
                'message' => 'Assinatura invalida.',
                'code' => 'invalid_signature',
            ], 401);
        }

        $existingEvent = IntegrationEvent::query()
            ->where('provider', 'e_grocery')
            ->where('event_id', $eventId)
            ->first();

        if ($existingEvent) {
            Log::channel('e_grocery_integration')->info('Duplicate webhook ignored', [
                'integration_event_id' => $existingEvent->id,
                'event_id' => $eventId,
                'event_type' => $eventType,
            ]);

            return response()->json([
                'message' => 'Evento duplicado. Ignorado com sucesso.',
                'status' => 'duplicate_ignored',
                'event_id' => $eventId,
            ], 200);
        }

        $integrationEvent = IntegrationEvent::query()->firstOrCreate(
            [
                'provider' => 'e_grocery',
                'event_id' => $eventId,
            ],
            [
                'event_type' => $eventType,
                'occurred_at' => CarbonImmutable::parse((string) $payload['occurred_at']),
                'status' => 'accepted',
                'payload' => $payload,
                'headers' => [
                    'x_event_id' => $eventId,
                    'x_event_type' => $eventType,
                    'x_event_time' => $eventTime,
                ],
            ]
        );

        if (!$integrationEvent->wasRecentlyCreated) {
            Log::channel('e_grocery_integration')->info('Duplicate webhook ignored after firstOrCreate', [
                'integration_event_id' => $integrationEvent->id,
                'event_id' => $eventId,
                'event_type' => $eventType,
            ]);

            return response()->json([
                'message' => 'Evento duplicado. Ignorado com sucesso.',
                'status' => 'duplicate_ignored',
                'event_id' => $eventId,
            ], 200);
        }

        ProcessEGroceryWebhookEventJob::dispatch($integrationEvent->id);

        Log::channel('e_grocery_integration')->info('Webhook accepted and queued', [
            'integration_event_id' => $integrationEvent->id,
            'event_id' => $eventId,
            'event_type' => $eventType,
            'event_time' => $eventTime,
            'received_at' => now()->toIso8601String(),
        ]);

        return response()->json([
            'message' => 'Evento aceito para processamento assíncrono.',
            'status' => 'accepted',
            'phase' => 'phase_2_idempotency_queue',
            'event_id' => $eventId,
        ], 202);
    }
}
