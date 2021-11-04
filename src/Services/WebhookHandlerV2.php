<?php


namespace Revosystems\Redsys\Services;

use Revosystems\Redsys\Lib\Model\Element\RESTOperationElement;
use Revosystems\Redsys\Lib\Model\Message\RESTAuthenticationRequestOperationMessage;
use Illuminate\Http\Request;

class WebhookHandlerV2 extends WebhookHandler
{
    protected function challenge(RedsysPayment $chargePayment, RESTAuthenticationRequestOperationMessage $challengeRequest) : void
    {
        $challengeRequest->challengeRequestV2($chargePayment->protocolVersion, $chargePayment->cres);
    }
}
