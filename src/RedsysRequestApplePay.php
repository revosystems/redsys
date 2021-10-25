<?php


namespace Revosystems\RedsysGateway;

use Revosystems\RedsysGateway\Lib\Constants\RESTConstants;
use Revosystems\RedsysGateway\Lib\Model\Message\RESTAuthorizationRequestOperationMessage;
use Revosystems\RedsysGateway\Lib\Service\Impl\RESTTrataRequestService;
use Revosystems\RedsysGateway\Models\ChargeRequest;
use Illuminate\Support\Facades\Log;
use Revosystems\RedsysGateway\Models\ChargeResult;

class RedsysRequestApplePay extends RedsysRequest
{
    protected function operationMessageClass()
    {
        return RESTAuthorizationRequestOperationMessage::class;
    }

    public function handle(ChargeRequest $data, $posOrderId, $amount, $currency, $payData)
    {
        $requestOperation = $this->requestOperation($data, $posOrderId, $amount, $currency);
        $requestOperation->useDirectPayment();
        $requestOperation->addParameter("DS_XPAYDATA", bin2Hex($payData));
        $requestOperation->addParameter("DS_XPAYTYPE", "Apple");
        $requestOperation->addParameter("DS_XPAYORIGEN", 'WEB');

        $response = RedsysRest::make(RESTTrataRequestService::class, $this->config->key, $this->config->test)
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