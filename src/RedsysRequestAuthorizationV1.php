<?php


namespace Revosystems\RedsysGateway;

use Revosystems\RedsysGateway\Models\ChargeRequest;

class RedsysRequestAuthorizationV1 extends RedsysRequestAuthorization
{
    public function handle(ChargeRequest $data, $posOrderId, $amount, $currency)
    {
        $operationRequest = $this->requestOperation($data, $posOrderId, $amount, $currency);
        $operationRequest->setEMV3DSParamsV1();
        return $this->getAuthorizationChargeResult($data, $operationRequest, $posOrderId);
    }

    protected function getWebhookClass()
    {
        return RedsysWebhookV1::class;
    }
}
