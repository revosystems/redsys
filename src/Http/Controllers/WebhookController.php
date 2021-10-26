<?php

namespace Revosystems\RedsysPayment\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Revosystems\RedsysPayment\Exceptions\SessionExpiredException;
use Revosystems\RedsysPayment\Interfaces\PaymentHandler;
use Revosystems\RedsysPayment\Services\RedsysException;
use Revosystems\RedsysPayment\Services\Webhook;

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

        if (! $handler = $this->paymentHandler($request)) {
            $this->chargeFailed($request, 'Charge unserialize handler failed');
            return;
        }

        try {
            $result = $this->webhookManager()->handle($operation, $request->get('orderId'), $this->unserialize('paymentHandler'), $request);
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

    protected function cachedData(Request $request) : ?array
    {
        $cachedData = Cache::get("rv-redsys-payment.webhooks.{$request->get('orderReference')}");
        Cache::forget("rv-redsys-payment.webhooks.{$request->get('orderReference')}");
        return $cachedData;
    }

    protected function paymentHandler(Request $request) : PaymentHandler
    {
        $paymentHandler = unserialize(Cache::get("rv-redsys-payment.orders.{$request->get('orderReference')}"));
        if (! $paymentHandler) {
            throw new SessionExpiredException;
        }
        Cache::forget("rv-redsys-payment.orders.{$request->get('orderReference')}");
        return $paymentHandler;
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
        Cache::put("rv-redsys-payment.webhooks.{$request->get('orderReference')}.result", 'FAILED', Carbon::now()->addMinutes(30));
    }

    protected function chargeSucceeded(Request $request)
    {
        Cache::put("rv-redsys-payment.webhooks.{$request->get('orderReference')}.result", 'SUCCESS', Carbon::now()->addMinutes(30));
    }

    protected function webhookManager() : Webhook
    {
        return $this->unserialize('redsysWebhook');
    }

    protected function unserialize(string $key, $decode = false)
    {
        if (! isset($this->cachedData[$key])) {
            return null;
        }
        return unserialize($decode ? base64_decode($this->cachedData[$key]) : $this->cachedData[$key]);
    }
}
