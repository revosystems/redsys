<?php

namespace Revosystems\Redsys\Services;

use Illuminate\Support\Facades\Log;
use Revosystems\Redsys\Lib\Constants\RESTConstants;
use Revosystems\Redsys\Lib\Model\Message\RESTRequestOperationMessage;
use Revosystems\Redsys\Lib\Service\Impl\RESTTrataRequestService;
use Revosystems\Redsys\Models\CardsTokenizable;
use Revosystems\Redsys\Models\ChargeResult;
use Revosystems\Redsys\Models\GatewayCard;

abstract class RequestAuthorization extends RedsysRequest
{
    protected $webhookHandler;

    protected function getAuthorizationChargeResult(RedsysChargeRequest $chargeRequest, RESTRequestOperationMessage $operationRequest, string $orderId) : ChargeResult
    {
        if ($chargeRequest->customerToken) {
            $operationRequest->createReference();
        }

        $response = RedsysRest::make(RESTTrataRequestService::class, $this->config->key)->sendOperation($operationRequest);
        $result   = $response->getResult();
        Log::debug("[REDSYS] Getting response {$result}");
        if ($result == RESTConstants::$RESP_LITERAL_KO) {
            Log::error("[REDSYS] Operation authorization was not OK");
            return new ChargeResult(false, $this->getResponse($response));
        }

        if ($result == RESTConstants::$RESP_LITERAL_OK) {
            Log::debug("[REDSYS] Operation authorization was OK (frictionless)");
            $operation = $response->getOperation();
            if ($chargeRequest->customerToken && $operation->getMerchantIdentifier()) {
                CardsTokenizable::tokenize(GatewayCard::makeFromOperation($operation), $chargeRequest->customerToken);
            }

            return new ChargeResult(true, $this->getResponse($response), $operationRequest->getAmount(), "redsys:{$chargeRequest->orderReference}");
        }
        
        return (new RedsysChallengeForm($this->webhookHandler))->display($chargeRequest, $response, $this->getWebhookUrl($chargeRequest->orderReference, $orderId), $operationRequest->getAmount());
    }

    protected function getWebhookUrl($orderReference, $orderId) : string
    {
        return "https://9a35-213-148-199-203.ngrok.io/webhooks/redsys?orderReference={$orderReference}&orderId={$orderId}";
        return route('webhooks.redsys', compact('orderReference', 'orderId'));
    }
}
