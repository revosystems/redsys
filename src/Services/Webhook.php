<?php


namespace Revosystems\Redsys\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Revosystems\Redsys\Lib\Constants\RESTConstants;
use Revosystems\Redsys\Lib\Model\Message\RESTAuthenticationRequestOperationMessage;
use Revosystems\Redsys\Lib\Model\Message\RESTResponseMessage;
use Revosystems\Redsys\Lib\Service\Impl\RESTTrataRequestService;
use Revosystems\Redsys\Models\CardsTokenizable;
use Revosystems\Redsys\Models\ChargeRequest;
use Revosystems\Redsys\Models\ChargeResult;
use Revosystems\Redsys\Models\GatewayCard;
use Revosystems\Redsys\Models\RedsysConfig;

abstract class Webhook
{
    const ORDERS_CACHE_KEY = 'redsys.orders.';
    /**
     * @var RedsysConfig
     */
    protected $config;

    public function __construct(RedsysConfig $config)
    {
        $this->config = $config;
    }

    /**
     * @throws RedsysException
     */
    public function handle($operation, $orderId, ChargeRequest $chargeRequest, Request $request)
    {
        $challengeRequest = $this->getRequestOperation($chargeRequest, $orderId, $operation->getAmount(), $operation->getCurrency());
        $this->challenge($challengeRequest, $operation, $request);
        return $this->sendAuthenticationConfirmationOperation($challengeRequest, $chargeRequest);
    }

    abstract protected function challenge(RESTAuthenticationRequestOperationMessage $challengeRequest, $operation, Request $request): void;

    protected function sendAuthenticationConfirmationOperation($challengeRequest, ChargeRequest $chargeRequest)
    {
        if ($chargeRequest->shouldSaveCard) {
            $challengeRequest->createReference();
        }
        $response   = RedsysRest::make(RESTTrataRequestService::class, $this->config->key)->sendOperation($challengeRequest);
        $result     = $response->getResult();

        Log::debug("[REDSYS] Getting webhook authentication response {$result}");
        if ($result == RESTConstants::$RESP_LITERAL_KO) {
            Log::error("[REDSYS] Operation webhook authentication was not OK");
            return new ChargeResult(false, $this->getResponse($response));
        }
        $operation = $response->getOperation();
        if ($chargeRequest->shouldSaveCard && $operation->getMerchantIdentifier()) {
            CardsTokenizable::tokenize(GatewayCard::makeFromOperation($operation), $chargeRequest->customerToken);
        }
        return new ChargeResult(true, $this->getResponse($response));
    }

    protected function getResponse(RESTResponseMessage $response)
    {
        return [
            "result"    => $response->getResult(),
            "operation" => $response->getResult() !== 'KO' ? $response->getOperation() : null,
        ];
    }

    protected function getRequestOperation(ChargeRequest $chargeRequest, $orderId, $amount, $currency)
    {
        return (new RESTAuthenticationRequestOperationMessage)
            ->setMerchant($this->config->code)
            ->setTerminal($this->config->terminal)
            ->generate($chargeRequest, $orderId, $amount, $currency);
    }
}
