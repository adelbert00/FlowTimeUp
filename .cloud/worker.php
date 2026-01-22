<?php

/**
 * Laravel Cloud Worker Configuration
 * 
 * This file configures the queue worker for Laravel Cloud.
 */

return [
    'connection' => env('QUEUE_CONNECTION', 'database'),
    'queues' => ['default', 'emails', 'notifications'],
    'tries' => 3,
    'timeout' => 60,
    'sleep' => 3,
    'memory' => 128,
];
