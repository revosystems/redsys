<?php

namespace Revosystems\Redsys\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Revosystems\Redsys\Lib\Model\Element\RESTOperationElement;
use Revosystems\Redsys\Services\RedsysChargeRequest;
use Revosystems\Redsys\Services\RedsysException;
use Revosystems\Redsys\Services\RedsysPayment;
use Revosystems\Redsys\Services\WebhookHandler;

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
        if (! $cachedData = WebhookHandler::get($request->get('paymentReference'))) {
            $this->chargeFailed($request, 'Charge retrieve cached data failed');
            return;
        }
        if (! $operation = $this->unserializeOperation($cachedData)) {
            $this->chargeFailed($request, 'Charge unserialize operation failed');
            return;
        }

        try {
            $result = $this->unserializeWebhookHandler($cachedData)->handle(
                $this->redsysPayment($request, $operation), $this->unserializeChargeRequest($cachedData), $this->isTest($cachedData)
            );
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
        if (! $request->get('paymentReference')) {
            return false;
        }
        return $request->get('cres') || ($request->get('PaRes') && $request->get('MD'));
    }

    protected function chargeFailed(Request $request, $errorMessage)
    {
        Log::error("[REDSYS] Webhook Error: {$errorMessage}");
        Cache::put(WebhookHandler::ORDERS_CACHE_KEY . "{$request->get('paymentReference')}.result", 'FAILED', Carbon::now()->addMinutes(30));
    }

    protected function chargeSucceeded(Request $request)
    {
        Cache::put(WebhookHandler::ORDERS_CACHE_KEY . "{$request->get('paymentReference')}.result", 'SUCCESS', Carbon::now()->addMinutes(30));
    }

    protected function unserialize(array $cachedData, string $key, $decode = false)
    {
        if (! isset($cachedData[$key])) {
            return null;
        }
        return unserialize($decode ? base64_decode($cachedData[$key]) : $cachedData[$key]);
    }

    protected function unserializeWebhookHandler(?array $cachedData) : WebhookHandler
    {
        return $this->unserialize($cachedData, 'webhookHandler');
    }

    protected function unserializeChargeRequest(?array $cachedData) : RedsysChargeRequest
    {
        return $this->unserialize($cachedData, 'chargeRequest');
    }

    protected function unserializeOperation(?array $cachedData) : ?RESTOperationElement
    {
        return $this->unserialize($cachedData, 'operation', true);
    }

    protected function isTest(?array $cachedData): bool
    {
        return $cachedData['test'] ?? false;
    }

    protected function redsysPayment(Request $request, RESTOperationElement $operation) : RedsysPayment
    {
        return (new RedsysPayment(
            $request->get('externalReference'), $request->get('tenant'),
            $operation->getAmount(), $operation->getCurrency()
        ))->setCres($request->get('cres'))
            ->setProtocolVersion(json_decode($operation->getEmv(), true)["protocolVersion"])
            ->setPaResAndMD($request->get('PaRes'), $request->get('MD'));
    }
}
