<?php

namespace Revosystems\RedsysGateway\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Revosystems\RedsysGateway\Models\ChargeRequest;
use Revosystems\RedsysGateway\RedsysException;

class WebhookController extends Controller
{
    protected $cachedData;

    public function webhook(Request $request) : void
    {
        Log::debug("[REDSYS] Payload received");
//        Log::debug($request->toArray());
        if (! $this->validateRequest($request)) {
            $this->chargeFailed($request, 'Charge request validation failed');
            return;
        }
        if (! $this->cachedData = $this->cachedData($request)) {
            $this->chargeFailed($request, 'Charge retrieve cached data failed');
            return;
        }
        if (! $operation = $this->unserialize('operation', true)) {
            $this->chargeFailed($request, 'Charge unserialize operation failed');
            return;
        }

        if (! $handler = $this->payHandler($request)) {
            $this->chargeFailed($request, 'Charge unserialize handler failed');
            return;
        }

        try {
            $result = $this->unserialize('redsysWebhook')->handle($operation, $request->get('posOrderId'), $this->unserialize('data'), $request);
            if (! $result->success) {
                $this->chargeFailed($request, 'Charge authentication failed');
                return;
            }
            Log::debug('[REDSYS] Authenticate charge success');
            $handler->onPaymentCompleted($result);
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

    protected function cachedData(Request $request) : ChargeRequest
    {
        $cachedData = Cache::get("redsys.webhooks.{$request->get('orderIdentifier')}");
        Cache::forget("redsys.webhooks.{$request->get('orderIdentifier')}");
        return $cachedData;
    }

    protected function payHandler(Request $request)
    {
        $payHandler = unserialize(Cache::get("redsys.{$request->get('orderIdentifier')}"));
        Cache::forget("redsys.{$request->get('orderIdentifier')}");
        return $payHandler;
    }

    protected function validateRequest(Request $request) : bool
    {
        if (! $request->get('orderIdentifier')) {
            return false;
        }
        return $request->get('cres') || ($request->get('PaRes') && $request->get('MD'));
    }

    protected function chargeFailed(Request $request, $errorMessage)
    {
        Log::error("[REDSYS] Webhook Error: {$errorMessage}");
        Cache::put("redsys.webhooks.{$request->get('orderIdentifier')}.result", 'FAILED', now()->addMinutes(30));
    }

    protected function chargeSucceeded(Request $request)
    {
        Cache::put("redsys.webhooks.{$request->get('orderIdentifier')}.result", 'SUCCESS', now()->addMinutes(30));
    }

    protected function unserialize(string $key, $decode = false) : ?ChargeRequest
    {
        if (! isset($this->cachedData[$key])) {
            return null;
        }
        return unserialize($decode ? base64_decode($this->cachedData[$key]) : $this->cachedData[$key]);
    }
}
