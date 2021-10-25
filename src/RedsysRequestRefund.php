<?php


namespace Revosystems\RedsysGateway;

use Revosystems\RedsysGateway\Models\ChargeResult;
use Revosystems\RedsysGateway\Lib\Constants\RESTConstants;
use Revosystems\RedsysGateway\Lib\Model\Message\RESTRefundRequestOperationMessage;
use Revosystems\RedsysGateway\Lib\Service\Impl\RESTTrataRequestService;
use Revosystems\RedsysGateway\Models\ChargeRequest;
use Illuminate\Support\Facades\Log;

class RedsysRequestRefund extends RedsysRequest
{
    public function handle($reference, $amount, $currency) : ChargeResult
    {
        $requestOperation = $this->requestOperation(new ChargeRequest(null, null, $reference), null, $amount, $currency);

        $response = RedsysRest::make(RESTTrataRequestService::class, $this->config->key, $this->config->test)
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
