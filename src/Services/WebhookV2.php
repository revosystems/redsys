<?php


namespace Revosystems\RedsysPayment\Services;

use Revosystems\RedsysPayment\Lib\Model\Message\RESTAuthenticationRequestOperationMessage;
use Illuminate\Http\Request;

class WebhookV2 extends Webhook
{
    protected function challenge(RESTAuthenticationRequestOperationMessage $challengeRequest, $operation, Request $request): void
    {
        $challengeRequest->challengeRequestV2(json_decode($operation->getEmv(), true)["protocolVersion"], $request->get('cres'));
    }
}