<?php


namespace Revosystems\Redsys\Services;

use Revosystems\Redsys\Lib\Model\Message\RESTResponseMessage;
use Revosystems\Redsys\Models\ChargeResult;

class RedsysChallengeForm
{
    protected $webhookHandler;

    public function __construct(WebhookHandler $webhookHandler)
    {
        $this->webhookHandler = $webhookHandler;
    }

    public function display(RedsysChargeRequest $chargeRequest, RESTResponseMessage $response, $termUrl, $amount) : ChargeResult
    {
        $operation  = $response->getOperation();
        $this->webhookHandler->persist($chargeRequest, $operation);
        return new ChargeResult(true, [
            "result"        => $response->getResult(),
            "displayForm"   => view('redsys::redsys.challenge', [
                'acsURL'    => $response->getAcsURLParameter(),
                'creq'      => $response->getCreqParameter(),
                'PaReq'     => $response->getPAReqParameter(),
                'md'        => $response->getMDParameter(),
                'termUrl'   => $termUrl,
            ])->toHtml(),
            "operation"     => $operation,
        ], $amount, "redsys:{$chargeRequest->orderReference}");
    }
}
