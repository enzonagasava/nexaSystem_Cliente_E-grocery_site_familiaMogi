<?php

namespace App\Services\Integrations;

use App\Models\EGroceryOrderExport;
use App\Models\IntegrationEvent;

class EGroceryIntegrationHealthService
{
    public function snapshot(int $hoursBack = 24): array
    {
        $hoursBack = max(1, $hoursBack);
        $from = now()->subHours($hoursBack);

        $eventStatusCounts = IntegrationEvent::query()
            ->where('provider', 'e_grocery')
            ->where('created_at', '>=', $from)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $orderExportStatusCounts = EGroceryOrderExport::query()
            ->where('created_at', '>=', $from)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $oldestProcessingEvent = IntegrationEvent::query()
            ->where('provider', 'e_grocery')
            ->whereIn('status', ['accepted', 'processing'])
            ->orderBy('created_at')
            ->first();

        $oldestQueuedOrderExport = EGroceryOrderExport::query()
            ->whereIn('status', ['queued', 'processing', 'failed'])
            ->orderBy('created_at')
            ->first();

        $snapshot = [
            'window_hours' => $hoursBack,
            'generated_at' => now()->toIso8601String(),
            'events' => [
                'accepted' => (int) ($eventStatusCounts['accepted'] ?? 0),
                'processing' => (int) ($eventStatusCounts['processing'] ?? 0),
                'processed' => (int) ($eventStatusCounts['processed'] ?? 0),
                'failed' => (int) ($eventStatusCounts['failed'] ?? 0),
                'total' => (int) array_sum($eventStatusCounts),
            ],
            'order_exports' => [
                'queued' => (int) ($orderExportStatusCounts['queued'] ?? 0),
                'processing' => (int) ($orderExportStatusCounts['processing'] ?? 0),
                'sent' => (int) ($orderExportStatusCounts['sent'] ?? 0),
                'failed' => (int) ($orderExportStatusCounts['failed'] ?? 0),
                'total' => (int) array_sum($orderExportStatusCounts),
            ],
            'stale' => [
                'oldest_processing_event' => $oldestProcessingEvent ? [
                    'id' => $oldestProcessingEvent->id,
                    'event_id' => $oldestProcessingEvent->event_id,
                    'status' => $oldestProcessingEvent->status,
                    'created_at' => optional($oldestProcessingEvent->created_at)->toIso8601String(),
                    'age_minutes' => (int) optional($oldestProcessingEvent->created_at)->diffInMinutes(now()),
                ] : null,
                'oldest_pending_order_export' => $oldestQueuedOrderExport ? [
                    'id' => $oldestQueuedOrderExport->id,
                    'external_order_id' => $oldestQueuedOrderExport->external_order_id,
                    'status' => $oldestQueuedOrderExport->status,
                    'created_at' => optional($oldestQueuedOrderExport->created_at)->toIso8601String(),
                    'age_minutes' => (int) optional($oldestQueuedOrderExport->created_at)->diffInMinutes(now()),
                ] : null,
            ],
        ];

        $snapshot['alerts'] = $this->buildAlerts($snapshot);
        $snapshot['status'] = $snapshot['alerts'] === [] ? 'ok' : 'warning';

        return $snapshot;
    }

    private function buildAlerts(array $snapshot): array
    {
        $thresholds = (array) config('integrations.e_grocery.health_thresholds', []);
        $alerts = [];

        $processingEvents = (int) ($snapshot['events']['processing'] ?? 0);
        $failedEvents = (int) ($snapshot['events']['failed'] ?? 0);
        $queuedOrderExports = (int) ($snapshot['order_exports']['queued'] ?? 0);
        $failedOrderExports = (int) ($snapshot['order_exports']['failed'] ?? 0);
        $oldestEventAgeMinutes = (int) ($snapshot['stale']['oldest_processing_event']['age_minutes'] ?? 0);

        if ($processingEvents > (int) ($thresholds['max_processing_events'] ?? 100)) {
            $alerts[] = sprintf('Processing events above threshold: %d', $processingEvents);
        }

        if ($failedEvents > (int) ($thresholds['max_failed_events'] ?? 20)) {
            $alerts[] = sprintf('Failed events above threshold: %d', $failedEvents);
        }

        if ($queuedOrderExports > (int) ($thresholds['max_queued_order_exports'] ?? 100)) {
            $alerts[] = sprintf('Queued order exports above threshold: %d', $queuedOrderExports);
        }

        if ($failedOrderExports > (int) ($thresholds['max_failed_order_exports'] ?? 20)) {
            $alerts[] = sprintf('Failed order exports above threshold: %d', $failedOrderExports);
        }

        if (
            $oldestEventAgeMinutes > 0 &&
            $oldestEventAgeMinutes > (int) ($thresholds['max_stale_processing_minutes'] ?? 30)
        ) {
            $alerts[] = sprintf('Oldest processing event is stale (%d minutes)', $oldestEventAgeMinutes);
        }

        return $alerts;
    }
}

