<?php


namespace Revosystems\RedsysPayment\Services;

use Revosystems\RedsysPayment\Models\ChargeResult;
use Revosystems\RedsysPayment\Lib\Constants\RESTConstants;
use Revosystems\RedsysPayment\Lib\Model\Message\RESTRefundRequestOperationMessage;
use Revosystems\RedsysPayment\Lib\Service\Impl\RESTTrataRequestService;
use Revosystems\RedsysPayment\Models\ChargeRequest;
use Illuminate\Support\Facades\Log;

class RedsysRequestRefund extends RedsysRequest
{
    public function handle($orderReference, $amount, $currency) : ChargeResult
    {
        $requestOperation = $this->requestOperation(new ChargeRequest($orderReference), null, $amount, $currency);

        $response = RedsysRest::make(RESTTrataRequestService::class, $this->config->key)
            ->sendOperation($requestOperation);

        $result   = $response->getResult();
        Log::debug("[REDSYS] Getting refund response {$result}");
        if ($result == RESTConstants::$RESP_LITERAL_KO) {
            Log::error("[REDSYS] Operation `REFUND` was not OK");
            return new ChargeResult(false, $this->getResponse($response), $amount, $orderReference);
        }
        return new ChargeResult(true, $this->getResponse($response), $amount, $orderReference);
    }

    protected function operationMessageClass()
    {
        return RESTRefundRequestOperationMessage::class;
    }
}
