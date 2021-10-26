<?php


namespace Revosystems\RedsysPayment\Services;

use Revosystems\RedsysPayment\Models\ChargeResult;
use Revosystems\RedsysPayment\Lib\Constants\RESTConstants;
use Revosystems\RedsysPayment\Lib\Model\Message\RESTInitialRequestOperationMessage;
use Revosystems\RedsysPayment\Lib\Service\Impl\RESTInitialRequestService;
use Revosystems\RedsysPayment\Models\ChargeRequest;
use Illuminate\Support\Facades\Log;

class RedsysRequestInit extends RedsysRequest
{
    public function handle(ChargeRequest $chargeRequest, $orderId, $amount, $currency)
    {
        $requestOperation = $this->requestOperation($chargeRequest, $orderId, $amount, $currency)
            ->demandCardData();

        $response = RedsysRest::make(RESTInitialRequestService::class, $this->config->key)
            ->sendOperation($requestOperation);

        $result   = $response->getResult();
        Log::debug("[REDSYS] Getting response {$result}");
        if ($result == RESTConstants::$RESP_LITERAL_KO) {
            Log::error("[REDSYS] Operation `Inicia Petición` was not OK");
            return new ChargeResult(false, $this->getResponse($response), $amount, $chargeRequest->orderReference);
        }
        return $response;
    }

    protected function operationMessageClass()
    {
        return RESTInitialRequestOperationMessage::class;
    }
}
