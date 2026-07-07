<?php

return [
    'e_grocery' => [
        'base_url' => rtrim((string) env('EGROCERY_API_BASE_URL', ''), '/'),
        'api_token' => env('EGROCERY_API_TOKEN'),
        'webhook_secret' => env('EGROCERY_WEBHOOK_SECRET'),
        'timeout_seconds' => (int) env('EGROCERY_TIMEOUT_SECONDS', 10),
        'health_thresholds' => [
            'max_processing_events' => (int) env('EGROCERY_MAX_PROCESSING_EVENTS', 100),
            'max_failed_events' => (int) env('EGROCERY_MAX_FAILED_EVENTS', 20),
            'max_queued_order_exports' => (int) env('EGROCERY_MAX_QUEUED_ORDER_EXPORTS', 100),
            'max_failed_order_exports' => (int) env('EGROCERY_MAX_FAILED_ORDER_EXPORTS', 20),
            'max_stale_processing_minutes' => (int) env('EGROCERY_MAX_STALE_PROCESSING_MINUTES', 30),
        ],
    ],
];
