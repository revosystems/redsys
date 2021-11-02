<?php


namespace Revosystems\Redsys\Services;

use Revosystems\Redsys\Lib\Model\Element\RESTOperationElement;
use Revosystems\Redsys\Lib\Model\Message\RESTAuthenticationRequestOperationMessage;
use Illuminate\Http\Request;

class WebhookHandlerV2 extends WebhookHandler
{
    protected function challenge(RESTAuthenticationRequestOperationMessage $challengeRequest, Request $request, RESTOperationElement $operation) : void
    {
        $challengeRequest->challengeRequestV2(json_decode($operation->getEmv(), true)["protocolVersion"], $request->get('cres'));
    }
}
