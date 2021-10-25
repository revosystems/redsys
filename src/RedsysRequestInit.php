<?php


namespace Revosystems\RedsysGateway;

use Revosystems\RedsysGateway\Models\ChargeResult;
use Revosystems\RedsysGateway\Lib\Constants\RESTConstants;
use Revosystems\RedsysGateway\Lib\Model\Message\RESTInitialRequestOperationMessage;
use Revosystems\RedsysGateway\Lib\Service\Impl\RESTInitialRequestService;
use Revosystems\RedsysGateway\Models\ChargeRequest;
use Illuminate\Support\Facades\Log;

class RedsysRequestInit extends RedsysRequest
{
    public function handle(ChargeRequest $data, $posOrderId, $amount, $currency)
    {
        $requestOperation = $this->requestOperation($data, $posOrderId, $amount, $currency)
            ->demandCardData();

        $response = RedsysRest::make(RESTInitialRequestService::class, $this->config->key, $this->config->test)
            ->sendOperation($requestOperation);

        $result   = $response->getResult();
        Log::debug("[REDSYS] Getting response {$result}");
        if ($result == RESTConstants::$RESP_LITERAL_KO) {
            Log::error("[REDSYS] Operation `Inicia Petición` was not OK");
            return new ChargeResult(false, $this->getResponse($response), $amount, $data->orderReference);
        }
        return $response;
    }

    protected function operationMessageClass()
    {
        return RESTInitialRequestOperationMessage::class;
    }
}
