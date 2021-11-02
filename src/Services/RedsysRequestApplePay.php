<?php


namespace Revosystems\Redsys\Services;

use Revosystems\Redsys\Lib\Constants\RESTConstants;
use Revosystems\Redsys\Lib\Model\Message\RESTAuthorizationRequestOperationMessage;
use Revosystems\Redsys\Lib\Service\Impl\RESTTrataRequestService;
use Illuminate\Support\Facades\Log;
use Revosystems\Redsys\Lib\Utils\Price;
use Revosystems\Redsys\Models\ChargeResult;

class RedsysRequestApplePay extends RedsysRequest
{
    public function handle(RedsysChargeRequest $chargeRequest, string $orderId, Price $price, $payData)
    {
        $requestOperation = (new RESTAuthorizationRequestOperationMessage)
            ->generate($this->config, $chargeRequest->orderReference, $orderId, $price);
        // Set card needed?
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
        return new ChargeResult(true, $this->getResponse($response), $requestOperation->getAmount(), "redsys:{$chargeRequest->orderReference}");
    }
}