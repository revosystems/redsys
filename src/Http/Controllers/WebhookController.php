<?php

namespace Revosystems\Redsys\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Revosystems\Redsys\Services\RedsysException;
use Revosystems\Redsys\Services\Webhook;
use Revosystems\Redsys\Services\WebhookManager;

class WebhookController extends Controller
{
    public function webhook(Request $request) : void
    {
        Log::debug("[REDSYS] Payload received");
//        Log::debug($request->toArray());
        if (! $this->validateRequest($request)) {
            $this->chargeFailed($request, 'Charge request validation failed');
            return;
        }
        if (! $cachedData = WebhookManager::get($request->get('orderReference'))) {
            $this->chargeFailed($request, 'Charge retrieve cached data failed');
            return;
        }
        if (! $operation = $this->unserialize($cachedData, 'operation', true)) {
            $this->chargeFailed($request, 'Charge unserialize operation failed');
            return;
        }

        try {
            $result = $this->unserialize($cachedData, 'webhook')
                ->handle($operation, $request->get('orderId'), $this->unserialize($cachedData, 'chargeRequest'), $request);
            if (! $result->success) {
                $this->chargeFailed($request, 'Charge authentication failed');
                return;
            }
            Log::debug('[REDSYS] Authenticate charge success');
            $this->chargeSucceeded($request);
            return;
        } catch (RedsysException $e) {
            $this->chargeFailed($request, "Charge failed {$e->getMessage()}");
            return;
        } catch (\Exception $e) {
            $this->chargeFailed($request, "Charge failed generic {$e->getMessage()}");
            return;
        }
    }

    protected function validateRequest(Request $request) : bool
    {
        if (! $request->get('orderReference')) {
            return false;
        }
        return $request->get('cres') || ($request->get('PaRes') && $request->get('MD'));
    }

    protected function chargeFailed(Request $request, $errorMessage)
    {
        Log::error("[REDSYS] Webhook Error: {$errorMessage}");
        Cache::put(Webhook::ORDERS_CACHE_KEY . "{$request->get('orderReference')}.result", 'FAILED', Carbon::now()->addMinutes(30));
    }

    protected function chargeSucceeded(Request $request)
    {
        Cache::put(Webhook::ORDERS_CACHE_KEY . "{$request->get('orderReference')}.result", 'SUCCESS', Carbon::now()->addMinutes(30));
    }

    protected function unserialize(array $cachedData, string $key, $decode = false)
    {
        if (! isset($cachedData[$key])) {
            return null;
        }
        return unserialize($decode ? base64_decode($cachedData[$key]) : $cachedData[$key]);
    }
}
