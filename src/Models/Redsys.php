<?php


namespace Revosystems\RedsysPayment\Models;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Revosystems\RedsysPayment\Exceptions\SessionExpiredException;
use Revosystems\RedsysPayment\Interfaces\Order;
use Revosystems\RedsysPayment\Interfaces\PaymentHandler;
use Revosystems\RedsysPayment\Lib\Constants\RESTConstants;
use Revosystems\RedsysPayment\Lib\Model\Element\RESTOperationElement;
use Revosystems\RedsysPayment\Models\Prices\Price;
use Revosystems\RedsysPayment\Services\RedsysRequestApplePay;
use Revosystems\RedsysPayment\Services\RedsysRequestGooglePay;
use Revosystems\RedsysPayment\Services\RedsysRequestInit;
use Revosystems\RedsysPayment\Services\RedsysRequestRefund;
use Revosystems\RedsysPayment\Services\RedsysTokenizableTrait;
use Revosystems\RedsysPayment\Services\RequestAuthorizationV1;
use Revosystems\RedsysPayment\Services\RequestAuthorizationV2;

class Redsys implements CardsTokenizable
{
    const PERSIST_KET = 'rv-redsys-payment';

    public $iframeUrl;
    /**
     * @var RedsysConfig
     */
    protected $config;
    public $paymentHandlerClass;

    public function __construct(RedsysConfig $config, $paymentHandlerClass)
    {
        $this->iframeUrl            = static::isTestEnvironment() ? 'https://sis-t.redsys.es:25443/sis/NC/sandbox/redsysV2.js' : 'https://sis.redsys.es/sis/NC/redsysV2.js';
        $this->config               = $config;
        $this->paymentHandlerClass  = $paymentHandlerClass;
    }

    public static function make(RedsysConfig $config, $paymentHandlerClass)
    {
        return (new self($config, $paymentHandlerClass))->persist();
    }

    public function merchantCode() : string
    {
        return $this->config->code;
    }

    public function merchantTerminal() : string
    {
        return $this->config->terminal;
    }

    public static function isTestEnvironment() : bool
    {
        return config('services.payment_gateways.redsys.test');
    }

    public function render(PaymentHandler $paymentHandler)
    {
        $orderReference = ChargeRequest::generateOrderReference();
        $paymentHandler->persist($orderReference);
        return view('redsys-payment::redsys.payment', [
            'orderReference'    => $orderReference,
            'orderId'   => $paymentHandler->order()->id(),
            'iframeUrl'         => $this->iframeUrl,
            'merchantCode'      => $this->merchantCode(),
            'merchantTerminal'  => $this->merchantTerminal(),
            'buttonText'        => __(config('redsys-payment.translationsPrefix') . 'pay') . ' ' . $paymentHandler->order()->price()->format(),
        ])->render();
    }

    public function charge(ChargeRequest $chargeRequest, Order $order) : ChargeResult
    {
        $idOper      = $chargeRequest->idOper;
        $cardId      = $chargeRequest->cardId;
        if ($idOper == -1 || (! $idOper && ! $cardId)) {
            return new ChargeResult(false, "No operation Id");
        }
        $response = (new RedsysRequestInit($this->config))
            ->handle($chargeRequest, $order->id(), $order->price()->amount/100, $order->price()->currency->numericCode());
        return $this->parseResult($response, $chargeRequest, $order->id(), $order->price()->amount, $order->price()->currency->numericCode());
    }

    protected function parseResult($response, ChargeRequest $chargeRequest, $orderId, $amount, $currency)
    {
        if ($response instanceof ChargeResult) {
            return $response;
        }
        if ($response->protocolVersionAnalysis() == RESTConstants::$REQUEST_MERCHANT_EMV3DS_PROTOCOLVERSION_102) {
            Log::debug('[REDSYS] Operation `Inicia Petición` requires authentication V1');
            return (new RequestAuthorizationV1($this->config))
                ->handle($chargeRequest, $orderId, $amount, $currency);
        }
        Log::debug('[REDSYS] Operation `Inicia Petición` requires authentication V2');
        return (new RequestAuthorizationV2($this->config))
            ->handle($chargeRequest, $orderId, $amount, $currency, $response);
    }

    public function chargeWithApple($orderId, $amount, $currency, $applePayData)
    {
        return (new RedsysRequestApplePay($this->config))
            ->handle(new ChargeRequest, $orderId, $amount, $currency, $applePayData);
    }

    public function chargeWithGoogle($orderId, $amount, $currency, $googlePayData)
    {
        return (new RedsysRequestGooglePay($this->config))
            ->handle(new ChargeRequest, $orderId, $amount, $currency, $googlePayData);
    }

    public function refundOrder($reference, $amount, $currency) : ChargeResult
    {
        return (new RedsysRequestRefund($this->config))
            ->handle($reference, $amount, $currency);
    }

    //==================================
    // METHODS TO PERSIST SECTION
    //==================================
    public function persist() : self
    {
        Session::put(static::PERSIST_KET, serialize($this));
        return $this;
    }

    public static function get() : self
    {
        if (! $handler = Session::get(static::PERSIST_KET)) {
            throw new SessionExpiredException();
        }
        return unserialize($handler);
    }

    //==================================
    // METHODS TO TOKENIZE CARDS SECTION
    //==================================
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
        Cache::put("redsys.cards.{$customerToken}", serialize($tokenizedCards), Carbon::now()->addMonths(4));
    }
}
