<?php


namespace Revosystems\Redsys\Services;

use Revosystems\Redsys\Lib\Model\Message\RESTAuthenticationRequestOperationMessage;

class WebhookHandlerV1 extends WebhookHandler
{
    protected function challenge(RedsysPayment $chargePayment, RESTAuthenticationRequestOperationMessage $challengeRequest) : void
    {
        $challengeRequest->challengeRequestV1($chargePayment->paRes, $chargePayment->mD);
    }
}
