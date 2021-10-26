<?php


namespace Revosystems\RedsysPayment\Services;

use Revosystems\RedsysPayment\Lib\Model\Message\RESTAuthenticationRequestOperationMessage;
use Illuminate\Http\Request;

class WebhookV1 extends Webhook
{
    protected function challenge(RESTAuthenticationRequestOperationMessage $challengeRequest, $operation, Request $request): void
    {
        $challengeRequest->challengeRequestV1($request->get('PaRes'), $request->get('MD'));
    }
}
