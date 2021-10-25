<?php


namespace Revosystems\RedsysGateway;

use Revosystems\RedsysGateway\Lib\Model\Message\RESTAuthenticationRequestOperationMessage;
use Illuminate\Http\Request;

class WebhookV1 extends Webhook
{
    protected function challenge(RESTAuthenticationRequestOperationMessage $challengeRequest, $operation, Request $request): void
    {
        $challengeRequest->challengeRequestV1($request->get('PaRes'), $request->get('MD'));
    }
}
