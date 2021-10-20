<?php


namespace Revosystems\RedsysGateway;

use Revosystems\RedsysGateway\Models\ChargeRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Revosystems\RedsysGateway\Models\CardsTokenizable;

class Redsys implements CardsTokenizable
{
    public $iframeUrl;
    /**
     * @var RedsysConfig
     */
    private $config;

    public function __construct(RedsysConfig $config)
    {
        $this->iframeUrl    = $config->test ? "https://sis-t.redsys.es:25443/sis/NC/sandbox/redsysV2.js" : "https://sis.redsys.es/sis/NC/redsysV2.js";
        $this->config       = $config;
    }

    public function merchantCode()
    {
        return $this->config->merchantCode;
    }

    public function merchantTerminal()
    {
        return $this->config->merchantTerminal;
    }

    public function isTestEnvironment()
    {
        return $this->config->test;
    }

    public function render($customerToken)
    {
        return view('views.redsys.payment', [
            'customerToken'     => $customerToken,
            'iframeUrl'         => $this->iframeUrl,
            'merchantCode'      => $this->merchantCode(),
//            'shouldSaveCard'    => $shouldSaveCard,
//            'cardId'            => $card_id,
            'merchantTerminal'  => $this->merchantTerminal(),
        ]);
    }

    public function charge(ChargeRequest $data, $posOrderId, $amount, $currency)
    {
        $response = (new RedsysRequestInit($this->config))
            ->handle($data, $posOrderId, $amount, $currency);
        return $this->parseResult($response, $data, $posOrderId, $amount, $currency);
    }

    public function chargeWithApple($posOrderId, $amount, $currency, $applePayData)
    {
        $orderId = Redsys::generateRandomOrderId();
        $data = new ChargeRequest(null, null, $orderId);
        return (new RedsysRequestApplePay($this->config))
            ->handle($data, $posOrderId, $amount, $currency, $applePayData);
    }

    public function chargeWithGoogle($posOrderId, $amount, $currency, $googlePayData)
    {
        $orderId = Redsys::generateRandomOrderId();
        $data = new ChargeRequest(null, null, $orderId);
        return (new RedsysRequestGooglePay($this->config))
            ->handle($data, $posOrderId, $amount, $currency, $googlePayData);
    }

    public function getCardsForCustomer($customerToken) : Collection
    {
        try {
            if (! $cachedCards = Cache::get("redsys.cards.{$customerToken}")) {
                return collect();
            }
            return collect(unserialize($cachedCards));
        } catch (\Exception $e) {
            Log::error("[REDSYS] Unserialize cards exception: {$e->getMessage()}");
            return collect();
        }
    }

    public static function tokenizeCards(RESTOperationElement $operation, $customerToken)
    {
        try {
            $tokenizedCards = unserialize(Cache::get("redsys.cards.{$customerToken}", []));
        } catch (\Exception $e) {
            Log::error("[REDSYS] Unserialize old cards exception: {$e->getMessage()}");
            $tokenizedCards = [];
        }
        $tokenizedCards[$operation->getCardNumber()] = new GatewayCard($operation->getMerchantIdentifier(), $operation->getCardNumber(), $operation->getExpiryDate());
        Cache::put("redsys.cards.{$customerToken}", serialize($tokenizedCards), now()->addMonths(4));
    }

    private function parseResult($response, ChargeRequest $data, $posOrderId, $amount, $currency): ChargeResult
    {
        if ($response instanceof ChargeResult) {
            return $response;
        }
        if ($response->protocolVersionAnalysis() == RESTConstants::$REQUEST_MERCHANT_EMV3DS_PROTOCOLVERSION_102) {
            Log::debug("[REDSYS] Operation `Inicia Petición` requires authentication V1");
            return (new RedsysRequestAuthorizationV1($this->config))
                ->handle($data, $posOrderId, $amount, $currency);
        }
        Log::debug("[REDSYS] Operation `Inicia Petición` requires authentication V2");
        return (new RedsysRequestAuthorizationV2($this->config))
            ->handle($data, $posOrderId, $amount, $currency, $response);
    }

    public function refundOrder($reference, $amount, $currency) : ChargeResult
    {
        return (new RedsysRequestRefund($this->config))
            ->handle($reference, $amount, $currency);
    }

    public static function generateRandomOrderId() : string {
        return substr(str_shuffle(str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789', 10)), 0, 12);
    }
}
