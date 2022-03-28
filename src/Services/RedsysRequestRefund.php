<?php


namespace Revosystems\Redsys\Services;

use Revosystems\Redsys\Models\ChargeResult;
use Revosystems\Redsys\Lib\Constants\RESTConstants;
use Revosystems\Redsys\Lib\Model\Message\RESTRefundRequestOperationMessage;
use Revosystems\Redsys\Lib\Service\Impl\RESTTrataRequestService;
use Illuminate\Support\Facades\Log;

class RedsysRequestRefund extends RedsysRequest
{
    public function handle(RedsysPayment $chargePayment, RedsysChargeRequest $chargeRequest) : ChargeResult
    {
        $requestOperation   = (new RESTRefundRequestOperationMessage)->generate($this->config, $chargePayment, $chargeRequest);
        Log::debug("[REDSYS] Operation");
        $response           = RedsysRest::make(RESTTrataRequestService::class, $this->config->key, $this->config->test)->sendOperation($requestOperation);
        Log::debug("[REDSYS] Response");
        $result             = $response->getResult();
        Log::debug("[REDSYS] Getting refund response {$result}");
        if ($result == RESTConstants::$RESP_LITERAL_KO) {
            Log::error("[REDSYS] Operation `REFUND` was not OK");
            return new ChargeResult(false, $this->getResponse($response), $chargeRequest->paymentReference);
        }
        return new ChargeResult(true, $this->getResponse($response), $chargeRequest->paymentReference);
    }
}
