<?php


namespace Revosystems\Redsys\Services;

use Illuminate\Http\Request;
use Revosystems\Redsys\Lib\Model\Element\RESTOperationElement;
use Revosystems\Redsys\Lib\Model\Message\RESTAuthenticationRequestOperationMessage;

class WebhookHandlerV1 extends WebhookHandler
{
    protected function challenge(RESTAuthenticationRequestOperationMessage $challengeRequest, Request $request, RESTOperationElement $operation) : void
    {
        $challengeRequest->challengeRequestV1($request->get('PaRes'), $request->get('MD'));
    }
}
