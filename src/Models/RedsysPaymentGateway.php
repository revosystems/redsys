<?php


namespace Revosystems\Redsys\Models;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Revosystems\Redsys\Exceptions\SessionExpiredException;
use Revosystems\Redsys\Interfaces\RedsysOrder;
use Revosystems\Redsys\Lib\Constants\RESTConstants;
use Revosystems\Redsys\Services\RedsysRequestApplePay;
use Revosystems\Redsys\Services\RedsysRequestGooglePay;
use Revosystems\Redsys\Services\RedsysRequestInit;
use Revosystems\Redsys\Services\RedsysRequestRefund;
use Revosystems\Redsys\Services\RequestAuthorizationV1;
use Revosystems\Redsys\Services\RequestAuthorizationV2;

class RedsysPaymentGateway
{
    const PERSIST_KET = 'redsys.payment-gateway';

    /**
     * @var RedsysConfig
     */
    protected $redsysConfig;

    public function __construct(RedsysConfig $redsysConfig)
    {
        $this->redsysConfig = $redsysConfig;
    }

    public static function make(RedsysConfig $config)
    {
        return (new self($config))->persist();
    }

    public static function isTestEnvironment() : bool
    {
        return config('services.payment_gateways.redsys.test');
    }

    public function render(PaymentHandler $paymentHandler, $customerToken)
    {
        $orderReference = ChargeRequest::generateOrderReference();
        $paymentHandler->persist($orderReference);
        return view('redsys::app.index', [
            'orderReference'    => $orderReference,
            'amount'            => $paymentHandler->order->price()->format(),
            'orderId'           => $paymentHandler->order->id(),
            'redsysConfig'      => $this->redsysConfig,
            'customerToken'     => $customerToken,
            'cards'             => CardsTokenizable::get($customerToken)
        ])->render();
    }

    public function charge(ChargeRequest $chargeRequest, RedsysOrder $order) : ChargeResult
    {
        $operationId = $chargeRequest->operationId;
        $cardId      = $chargeRequest->cardId;
        if ($operationId == -1 || (! $operationId && ! $cardId)) {
            return new ChargeResult(false, "No operation Id");
        }
        $response = (new RedsysRequestInit($this->redsysConfig))
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
            return (new RequestAuthorizationV1($this->redsysConfig))
                ->handle($chargeRequest, $orderId, $amount, $currency);
        }
        Log::debug('[REDSYS] Operation `Inicia Petición` requires authentication V2');
        return (new RequestAuthorizationV2($this->redsysConfig))
            ->handle($chargeRequest, $orderId, $amount, $currency, $response);
    }

    /*
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
    */

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
        if (! $paymentGateway = Session::get(static::PERSIST_KET)) {
            throw new SessionExpiredException();
        }
        return unserialize($paymentGateway);
    }
}
