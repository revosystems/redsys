<?php


namespace Revosystems\RedsysGateway;

use Revosystems\RedsysGateway\Lib\Model\Message\RESTAuthenticationRequestOperationMessage;
use Illuminate\Http\Request;

class RedsysWebhookV2 extends RedsysWebhook
{
    protected function challenge(RESTAuthenticationRequestOperationMessage $challengeRequest, $operation, Request $request): void
    {
        $challengeRequest->challengeRequestV2(json_decode($operation->getEmv(), true)["protocolVersion"], $request->get('cres'));
    }
}
