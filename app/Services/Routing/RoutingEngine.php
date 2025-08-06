<?php

namespace App\Services\Routing;

use App\Models\RoutingLog;

class RoutingEngine
{
    public static function decideProvider(array $orderData): string
    {
        return 'labayh';
    }

    public static function log(string $orderNumber, string $provider, array $context, string $reason): void
    {
        RoutingLog::create([
            'order_number' => $orderNumber,
            'chosen_provider' => $provider,
            'context' => $context,
            'reason' => $reason,
        ]);
    }
}
