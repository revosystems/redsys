<?php


namespace Revosystems\Redsys\Services;

use Revosystems\Redsys\Models\ChargeRequest;

class RequestAuthorizationV1 extends RequestAuthorization
{
    public function handle(ChargeRequest $chargeRequest, $orderId, $amount, $currency)
    {
        $operationRequest = $this->requestOperation($chargeRequest, $orderId, $amount, $currency);
        $operationRequest->setEMV3DSParamsV1();
        return $this->getAuthorizationChargeResult($chargeRequest, $operationRequest, $orderId);
    }

    protected function getWebhookClass()
    {
        return WebhookV1::class;
    }
}
