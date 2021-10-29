<?php


namespace Revosystems\Redsys\Services;

use Revosystems\Redsys\Models\ChargeResult;
use Revosystems\Redsys\Lib\Constants\RESTConstants;
use Revosystems\Redsys\Lib\Model\Message\RESTRefundRequestOperationMessage;
use Revosystems\Redsys\Lib\Service\Impl\RESTTrataRequestService;
use Revosystems\Redsys\Models\ChargeRequest;
use Illuminate\Support\Facades\Log;

class RedsysRequestRefund extends RedsysRequest
{
    public function handle($reference, $amount, $currency) : ChargeResult
    {
        $requestOperation = $this->requestOperation(new ChargeRequest($reference), null, $amount, $currency);

        $response = RedsysRest::make(RESTTrataRequestService::class, $this->config->key)
            ->sendOperation($requestOperation);

        $result   = $response->getResult();
        Log::debug("[REDSYS] Getting refund response {$result}");
        if ($result == RESTConstants::$RESP_LITERAL_KO) {
            Log::error("[REDSYS] Operation `REFUND` was not OK");
            return new ChargeResult(false, $this->getResponse($response), $amount, $reference);
        }
        return new ChargeResult(true, $this->getResponse($response), $amount, $reference);
    }

    protected function operationMessageClass()
    {
        return RESTRefundRequestOperationMessage::class;
    }
}
