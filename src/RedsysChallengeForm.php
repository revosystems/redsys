<?php


namespace Revosystems\RedsysGateway;

use Illuminate\Support\Facades\Cache;
use Revosystems\RedsysGateway\Lib\Model\Message\RESTResponseMessage;
use Revosystems\RedsysGateway\Models\ChargeRequest;
use Revosystems\RedsysGateway\Models\ChargeResult;

class RedsysChallengeForm
{
    protected $webhook;

    public function __construct($webhook)
    {
        $this->webhook = $webhook;
    }

    public function display(ChargeRequest $data, RESTResponseMessage $response, $termUrl, $amount) : ChargeResult
    {
        $operation  = base64_encode(serialize($response->getOperation()));
        Cache::put("redsys.webhooks.{$data->orderReference}", [
            'data'          => serialize($data),
            'operation'     => $operation,
            'redsysWebhook' => serialize($this->webhook),
        ], now()->addMinutes(30));
        return new ChargeResult(true, [
            "result"        => $response->getResult(),
            "displayForm"   => view('webapp.redsys.challenge', [
                'acsURL'    => $response->getAcsURLParameter(),
                'creq'      => $response->getCreqParameter(),
                'PaReq'     => $response->getPAReqParameter(),
                'md'        => $response->getMDParameter(),
                'termUrl'   => $termUrl,
            ])->toHtml(),
            "operation"     => $operation,
        ], $amount, "redsys:{$data->orderReference}");
    }
}
