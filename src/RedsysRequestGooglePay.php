<?php


namespace Revosystems\RedsysGateway;

use Revosystems\RedsysGateway\Models\ChargeResult;
use Revosystems\RedsysGateway\Lib\Constants\RESTConstants;
use Revosystems\RedsysGateway\Lib\Model\Message\RESTAuthorizationRequestOperationMessage;
use Revosystems\RedsysGateway\Lib\Service\Impl\RESTTrataRequestService;
use Revosystems\RedsysGateway\Models\ChargeRequest;
use Illuminate\Support\Facades\Log;

class RedsysRequestGooglePay extends RedsysRequest
{
    protected function operationMessageClass()
    {
        return RESTAuthorizationRequestOperationMessage::class;
    }

    public function handle(ChargeRequest $data, $posOrderId, $amount, $currency, $payData)
    {
        $requestOperation = $this->requestOperation($data, $posOrderId, $amount, $currency);
        $requestOperation->useDirectPayment();
        $requestOperation->addParameter("DS_XPAYDATA", base64_encode($payData));
        $requestOperation->addParameter("DS_XPAYTYPE", "Google");
        $requestOperation->addParameter("DS_XPAYORIGEN", 'WEB');

        $response = RedsysRest::make(RESTTrataRequestService::class, $this->config->claveComercio, $this->config->test)
            ->sendOperation($requestOperation);
        $result   = $response->getResult();
        Log::debug("[REDSYS] Getting google pay response {$result}");
        if ($result == RESTConstants::$RESP_LITERAL_KO) {
            Log::error("[REDSYS] Operation `GooglePay` was not OK");
            return new ChargeResult(false, $this->getResponse($response));
        }
        return new ChargeResult(true, $this->getResponse($response), $requestOperation->getAmount(), "redsys:{$data->orderId}");
    }
}