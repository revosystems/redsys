<?php


namespace Revosystems\Redsys\Services;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Revosystems\Redsys\Lib\Constants\RESTConstants;
use Revosystems\Redsys\Lib\Model\Element\RESTOperationElement;
use Revosystems\Redsys\Lib\Model\Message\RESTAuthenticationRequestOperationMessage;
use Revosystems\Redsys\Lib\Model\Message\RESTResponseMessage;
use Revosystems\Redsys\Lib\Service\Impl\RESTTrataRequestService;
use Revosystems\Redsys\Models\CardsTokenizable;
use Revosystems\Redsys\Models\ChargeResult;
use Revosystems\Redsys\Models\GatewayCard;
use Revosystems\Redsys\Models\RedsysConfig;

abstract class WebhookHandler
{
    const ORDERS_CACHE_KEY = 'redsys.orders.';
    protected $config;

    public function __construct(RedsysConfig $config)
    {
        $this->config = $config;
    }

    /**
     * @throws RedsysException
     */
    public function handle(RedsysPayment $chargePayment, RedsysChargeRequest $chargeRequest) : ChargeResult
    {
        $challengeRequest = (new RESTAuthenticationRequestOperationMessage)
            ->generate($this->config, $chargePayment, $chargeRequest)
            ->setCard($chargeRequest);
        $this->challenge($chargePayment, $challengeRequest);
        return $this->sendAuthenticationConfirmationOperation($challengeRequest, $chargeRequest);
    }

    abstract protected function challenge(RedsysPayment $chargePayment, RESTAuthenticationRequestOperationMessage $challengeRequest) : void;

    protected function sendAuthenticationConfirmationOperation($challengeRequest, RedsysChargeRequest $chargeRequest) : ChargeResult
    {
        if ($chargeRequest->customerToken) {
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
        if ($chargeRequest->customerToken && $operation->getMerchantIdentifier()) {
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

    public function persist(RedsysChargeRequest $chargeRequest, RESTOperationElement $operation)
    {
        Cache::put(static::ORDERS_CACHE_KEY . $chargeRequest->paymentReference, [
            'chargeRequest' => serialize($chargeRequest),
            'operation'     => base64_encode(serialize($operation)),
            'webhookHandler'=> serialize($this),
        ], Carbon::now()->addMinutes(30));
    }

    public static function get(string $paymentReference) : ?array
    {
        $cachedData = Cache::get(static::ORDERS_CACHE_KEY . $paymentReference);
        Cache::forget(static::ORDERS_CACHE_KEY . $paymentReference);
        return $cachedData;
    }
}
