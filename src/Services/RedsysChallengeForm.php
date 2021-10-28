<?php


namespace Revosystems\Redsys\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Revosystems\Redsys\Http\Livewire\Redsys;
use Revosystems\Redsys\Lib\Model\Message\RESTResponseMessage;
use Revosystems\Redsys\Models\ChargeRequest;
use Revosystems\Redsys\Models\ChargeResult;

class RedsysChallengeForm
{
    protected $webhook;

    public function __construct(Webhook $webhook)
    {
        $this->webhook = $webhook;
    }

    public function display(ChargeRequest $chargeRequest, RESTResponseMessage $response, $termUrl, $amount) : ChargeResult
    {
        $operation  = $response->getOperation();
        WebhookManager::save($this->webhook, $chargeRequest, $operation);
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
