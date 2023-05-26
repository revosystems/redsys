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

    protected function getAuthorizationChargeResult(RedsysChargePayment $chargePayment, RedsysChargeRequest $chargeRequest, RESTRequestOperationMessage $operationRequest) : ChargeResult
    {
        if ($chargeRequest->customerToken) {
            $operationRequest->createReference();
        }
        if ($chargeRequest->cardId && !$chargeRequest->extraInfo) {
            $operationRequest->useDirectPayment();
        }

        $response = RedsysRest::make(RESTTrataRequestService::class, $this->config->key, $this->config->test)->sendOperation($operationRequest);
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

            return new ChargeResult(true, $this->getResponse($response), "redsys:{$chargeRequest->paymentReference}");
        }
        
        return (new RedsysChallengeForm($this->webhookHandler))->display($chargeRequest, $response, $this->getWebhookUrl($chargePayment, $chargeRequest->paymentReference));
    }

    protected function getWebhookUrl(RedsysChargePayment $chargePayment, string $paymentReference) : string
    {
//        return "https://c273-213-148-218-55.ngrok.io/webhooks/redsys?"
//        . "paymentReference={$paymentReference}&"
//        . "externalReference={$chargePayment->externalReference}&"
//        . "tenant={$chargePayment->tenant}";
        return route('webhooks.redsys', [
            'paymentReference'  => $paymentReference,
            'externalReference' => $chargePayment->externalReference,
            'tenant'            => $chargePayment->tenant
        ]);
    }
}
