<?php


namespace Revosystems\Redsys\Services;

use Revosystems\Redsys\Lib\Model\Message\RESTResponseMessage;
use Revosystems\Redsys\Lib\Utils\Price;
use Revosystems\Redsys\Models\ChargeResult;
use Revosystems\Redsys\Lib\Constants\RESTConstants;
use Revosystems\Redsys\Lib\Model\Message\RESTInitialRequestOperationMessage;
use Revosystems\Redsys\Lib\Service\Impl\RESTInitialRequestService;
use Illuminate\Support\Facades\Log;

class RedsysRequestInit extends RedsysRequest
{
    public function handle(RedsysChargeRequest $chargeRequest, string $orderId, Price $price)
    {
        $requestOperation = (new RESTInitialRequestOperationMessage)
            ->generate($this->config, $chargeRequest->orderReference, $orderId, $price)
            ->setCard($chargeRequest)
            ->demandCardData();

        $response = RedsysRest::make(RESTInitialRequestService::class, $this->config->key)
            ->sendOperation($requestOperation);
        return $this->parseResult($chargeRequest, $orderId, $price, $response);
    }

    protected function parseResult(RedsysChargeRequest $chargeRequest, string $orderId, Price $price, RESTResponseMessage $response) : ChargeResult
    {
        $result = $response->getResult();
        Log::debug("[REDSYS] Getting response {$result}");
        if ($result == RESTConstants::$RESP_LITERAL_KO) {
            Log::error("[REDSYS] Operation `Inicia Petición` was not OK");
            return new ChargeResult(false, $this->getResponse($response), $price->amount/100, $chargeRequest->orderReference);
        }
        if ($response->protocolVersionAnalysis() == RESTConstants::$REQUEST_MERCHANT_EMV3DS_PROTOCOLVERSION_102) {
            Log::debug('[REDSYS] Operation `Inicia Petición` requires authentication V1');
            return (new RequestAuthorizationV1($this->config))
                ->handle($chargeRequest, $orderId, $price);
        }
        Log::debug('[REDSYS] Operation `Inicia Petición` requires authentication V2');
        return (new RequestAuthorizationV2($this->config))
            ->handle($chargeRequest, $orderId, $price, $response);
    }
}
