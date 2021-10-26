<?php


namespace Revosystems\RedsysPayment\Services;

use Revosystems\RedsysPayment\Lib\Constants\RESTConstants;
use Revosystems\RedsysPayment\Lib\Model\Message\RESTAuthorizationRequestOperationMessage;
use Revosystems\RedsysPayment\Lib\Service\Impl\RESTTrataRequestService;
use Revosystems\RedsysPayment\Models\ChargeRequest;
use Illuminate\Support\Facades\Log;
use Revosystems\RedsysPayment\Models\ChargeResult;

class RedsysRequestApplePay extends RedsysRequest
{
    protected function operationMessageClass()
    {
        return RESTAuthorizationRequestOperationMessage::class;
    }

    public function handle(ChargeRequest $data, $orderId, $amount, $currency, $payData)
    {
        $requestOperation = $this->requestOperation($data, $orderId, $amount, $currency);
        $requestOperation->useDirectPayment();
        $requestOperation->addParameter("DS_XPAYDATA", bin2Hex($payData));
        $requestOperation->addParameter("DS_XPAYTYPE", "Apple");
        $requestOperation->addParameter("DS_XPAYORIGEN", 'WEB');

        $response = RedsysRest::make(RESTTrataRequestService::class, $this->config->key)
            ->sendOperation($requestOperation);
        $result   = $response->getResult();
        Log::debug("[REDSYS] Getting apple pay response {$result}");
        if ($result == RESTConstants::$RESP_LITERAL_KO) {
            Log::error("[REDSYS] Operation `ApplePay` was not OK");
            return new ChargeResult(false, $this->getResponse($response));
        }
        return new ChargeResult(true, $this->getResponse($response), $requestOperation->getAmount(), "redsys:{$data->orderReference}");
    }
}