<?php


namespace Revosystems\RedsysPayment\Services;

use Revosystems\RedsysPayment\Models\ChargeRequest;

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
