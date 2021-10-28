<?php


namespace Revosystems\Redsys\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Revosystems\Redsys\Lib\Model\Element\RESTOperationElement;
use Revosystems\Redsys\Models\ChargeRequest;

class WebhookManager
{
    const ORDERS_CACHE_KEY = 'redsys.orders.';

    //    public function saveToCache(ChargeRequest $chargeRequest, string $operation)
    public static function save(Webhook $webhook, ChargeRequest $chargeRequest, RESTOperationElement $operation)
    {
        Cache::put(static::ORDERS_CACHE_KEY . $chargeRequest->orderReference, [
            'chargeRequest' => serialize($chargeRequest),
            'operation'     => base64_encode(serialize($operation)),
            'webhook'       => serialize($webhook),
        ], Carbon::now()->addMinutes(30));
    }

    public static function get(string $orderReference) : ?array
    {
        $cachedData = Cache::get(static::ORDERS_CACHE_KEY . $orderReference);
        Cache::forget(static::ORDERS_CACHE_KEY . $orderReference);
        return $cachedData;
    }
}
