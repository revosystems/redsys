<?php


namespace Revosystems\RedsysGateway;

use Revosystems\RedsysGateway\Models\ChargeRequest;

class RequestAuthorizationV1 extends RequestAuthorization
{
    public function handle(ChargeRequest $data, $posOrderId, $amount, $currency)
    {
        $operationRequest = $this->requestOperation($data, $posOrderId, $amount, $currency);
        $operationRequest->setEMV3DSParamsV1();
        return $this->getAuthorizationChargeResult($data, $operationRequest, $posOrderId);
    }

    protected function getWebhookClass()
    {
        return WebhookV1::class;
    }
}
