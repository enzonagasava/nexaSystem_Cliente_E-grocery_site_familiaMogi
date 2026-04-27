<?php

use App\Jobs\SendEGroceryOrderJob;
use App\Models\EGroceryOrderExport;
use App\Services\Integrations\EGroceryCatalogSyncService;
use App\Services\Integrations\EGroceryIntegrationHealthService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('integrations:e-grocery:sync-catalog {--updated-since=} {--skip-images} {--max-pages=0}', function (EGroceryCatalogSyncService $syncService) {
    $updatedSince = $this->option('updated-since');
    $skipImages = (bool) $this->option('skip-images');
    $maxPages = (int) $this->option('max-pages');

    $this->info('Starting e-grocery catalog sync...');
    $this->line('updated_since: '.($updatedSince ?: 'null'));
    $this->line('skip_images: '.($skipImages ? 'true' : 'false'));
    $this->line('max_pages: '.$maxPages);

    $stats = $syncService->sync(
        is_string($updatedSince) && trim($updatedSince) !== '' ? trim($updatedSince) : null,
        !$skipImages,
        $maxPages
    );

    $this->newLine();
    $this->info('Sync completed.');
    $this->table(
        ['Metric', 'Value'],
        [
            ['ads_synced', $stats['ads_synced']],
            ['products_synced', $stats['products_synced']],
            ['images_synced', $stats['images_synced']],
            ['pages_ads', $stats['pages_ads']],
            ['pages_products', $stats['pages_products']],
        ]
    );
})->purpose('Sync e-grocery catalog (ads, products, images) into local database cache');

Artisan::command('integrations:e-grocery:reconcile {--updated-since=} {--skip-images} {--max-pages=0} {--retry-failed-orders} {--retry-limit=100}', function (EGroceryCatalogSyncService $syncService) {
    $updatedSinceOption = $this->option('updated-since');
    $skipImages = (bool) $this->option('skip-images');
    $maxPages = (int) $this->option('max-pages');
    $retryFailedOrders = (bool) $this->option('retry-failed-orders');
    $retryLimit = max(1, (int) $this->option('retry-limit'));
    $updatedSince = is_string($updatedSinceOption) && trim($updatedSinceOption) !== ''
        ? trim($updatedSinceOption)
        : now()->subDay()->toIso8601String();

    $this->info('Starting e-grocery nightly reconciliation...');
    $this->line('updated_since: '.$updatedSince);
    $this->line('skip_images: '.($skipImages ? 'true' : 'false'));
    $this->line('max_pages: '.$maxPages);
    $this->line('retry_failed_orders: '.($retryFailedOrders ? 'true' : 'false'));

    try {
        $stats = $syncService->sync($updatedSince, !$skipImages, $maxPages);
    } catch (\Throwable $exception) {
        $this->error('Catalog reconciliation failed: '.$exception->getMessage());
        Log::channel('e_grocery_integration')->error('Nightly reconciliation failed', [
            'error' => $exception->getMessage(),
        ]);

        return self::FAILURE;
    }

    $retriedOrders = 0;
    if ($retryFailedOrders) {
        $ordersToRetry = EGroceryOrderExport::query()
            ->whereIn('status', ['failed', 'queued'])
            ->orderBy('created_at')
            ->limit($retryLimit)
            ->get(['id']);

        foreach ($ordersToRetry as $order) {
            SendEGroceryOrderJob::dispatch($order->id);
            $retriedOrders++;
        }
    }

    $this->newLine();
    $this->info('Reconciliation completed.');
    $this->table(
        ['Metric', 'Value'],
        [
            ['ads_synced', $stats['ads_synced']],
            ['products_synced', $stats['products_synced']],
            ['images_synced', $stats['images_synced']],
            ['pages_ads', $stats['pages_ads']],
            ['pages_products', $stats['pages_products']],
            ['orders_retried', $retriedOrders],
        ]
    );

    return self::SUCCESS;
})->purpose('Run nightly e-grocery reconciliation and optionally retry failed order exports');

Artisan::command('integrations:e-grocery:health-check {--hours=24}', function (EGroceryIntegrationHealthService $healthService) {
    $hours = max(1, (int) $this->option('hours'));
    $snapshot = $healthService->snapshot($hours);
    $alerts = (array) ($snapshot['alerts'] ?? []);

    $this->info('E-grocery integration health snapshot');
    $this->line('window_hours: '.$snapshot['window_hours']);
    $this->line('status: '.$snapshot['status']);

    $this->table(
        ['Metric', 'Value'],
        [
            ['events.accepted', $snapshot['events']['accepted']],
            ['events.processing', $snapshot['events']['processing']],
            ['events.processed', $snapshot['events']['processed']],
            ['events.failed', $snapshot['events']['failed']],
            ['order_exports.queued', $snapshot['order_exports']['queued']],
            ['order_exports.processing', $snapshot['order_exports']['processing']],
            ['order_exports.sent', $snapshot['order_exports']['sent']],
            ['order_exports.failed', $snapshot['order_exports']['failed']],
        ]
    );

    if ($alerts === []) {
        $this->info('No alerts.');
        return self::SUCCESS;
    }

    foreach ($alerts as $alert) {
        $this->warn('ALERT: '.$alert);
    }

    Log::channel('e_grocery_integration')->warning('Integration health check reported alerts', [
        'alerts' => $alerts,
        'snapshot' => $snapshot,
    ]);

    return self::FAILURE;
})->purpose('Check e-grocery integration health and emit alerts');

Schedule::command('integrations:e-grocery:reconcile --retry-failed-orders --retry-limit=200')
    ->dailyAt('02:00')
    ->name('e-grocery-nightly-reconciliation');

Schedule::command('integrations:e-grocery:health-check --hours=24')
    ->everyFifteenMinutes()
    ->name('e-grocery-health-check');
