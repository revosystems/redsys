<?php


namespace Revosystems\RedsysPayment\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Revosystems\RedsysPayment\Lib\Model\Message\RESTResponseMessage;
use Revosystems\RedsysPayment\Models\ChargeRequest;
use Revosystems\RedsysPayment\Models\ChargeResult;

class RedsysChallengeForm
{
    protected $webhook;

    public function __construct($webhook)
    {
        $this->webhook = $webhook;
    }

    public function display(ChargeRequest $paymentHandler, RESTResponseMessage $response, $termUrl, $amount) : ChargeResult
    {
        $operation  = base64_encode(serialize($response->getOperation()));
        Cache::put("rv-redsys-payment.webhooks.{$paymentHandler->orderReference}", [
            'paymentHandler'=> serialize($paymentHandler),
            'operation'     => $operation,
            'redsysWebhook' => serialize($this->webhook),
        ], Carbon::now()->addMinutes(30));
        return new ChargeResult(true, [
            "result"        => $response->getResult(),
            "displayForm"   => view('redsys-payment::redsys.challenge', [
                'acsURL'    => $response->getAcsURLParameter(),
                'creq'      => $response->getCreqParameter(),
                'PaReq'     => $response->getPAReqParameter(),
                'md'        => $response->getMDParameter(),
                'termUrl'   => $termUrl,
            ])->toHtml(),
            "operation"     => $operation,
        ], $amount, "redsys:{$paymentHandler->orderReference}");
    }
}
