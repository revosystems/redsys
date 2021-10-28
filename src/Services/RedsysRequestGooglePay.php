<?php


namespace Revosystems\Redsys\Services;

use Revosystems\Redsys\Models\ChargeResult;
use Revosystems\Redsys\Lib\Constants\RESTConstants;
use Revosystems\Redsys\Lib\Model\Message\RESTAuthorizationRequestOperationMessage;
use Revosystems\Redsys\Lib\Service\Impl\RESTTrataRequestService;
use Revosystems\Redsys\Models\ChargeRequest;
use Illuminate\Support\Facades\Log;

class RedsysRequestGooglePay extends RedsysRequest
{
    protected function operationMessageClass()
    {
        return RESTAuthorizationRequestOperationMessage::class;
    }

    public function handle(ChargeRequest $chargeRequest, $orderId, $amount, $currency, $payData)
    {
        $requestOperation = $this->requestOperation($chargeRequest, $orderId, $amount, $currency);
        $requestOperation->useDirectPayment();
        $requestOperation->addParameter("DS_XPAYDATA", base64_encode($payData));
        $requestOperation->addParameter("DS_XPAYTYPE", "Google");
        $requestOperation->addParameter("DS_XPAYORIGEN", 'WEB');

        $response = RedsysRest::make(RESTTrataRequestService::class, $this->config->key)
            ->sendOperation($requestOperation);
        $result   = $response->getResult();
        Log::debug("[REDSYS] Getting google pay response {$result}");
        if ($result == RESTConstants::$RESP_LITERAL_KO) {
            Log::error("[REDSYS] Operation `GooglePay` was not OK");
            return new ChargeResult(false, $this->getResponse($response));
        }
        return new ChargeResult(true, $this->getResponse($response), $requestOperation->getAmount(), "redsys:{$chargeRequest->orderReference}");
    }
}