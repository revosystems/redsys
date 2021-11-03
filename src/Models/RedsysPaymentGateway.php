<?php


namespace Revosystems\Redsys\Models;

use Illuminate\Support\Facades\Session;
use Revosystems\Redsys\Exceptions\SessionExpiredException;
use Revosystems\Redsys\Services\RedsysCharge;
use Revosystems\Redsys\Services\RedsysChargeRequest;
use Revosystems\Redsys\Services\RedsysRefund;
use Revosystems\Redsys\Services\RedsysRequestInit;
use Revosystems\Redsys\Services\RedsysRequestRefund;

class RedsysPaymentGateway
{
    const PERSIST_KET = 'redsys.payment-gateway';

    /**
     * @var RedsysConfig
     */
    protected $config;

    public function __construct(RedsysConfig $redsysConfig)
    {
        $this->config = $redsysConfig;
    }

    public static function make(RedsysConfig $config) : self
    {
        return (new self($config))->persist();
    }

    public static function isTestEnvironment() : bool
    {
        return config('services.payment_gateways.redsys.test');
    }

    public function render(RedsysCharge $redsysCharge, $customerToken)
    {
        $orderReference = RedsysChargeRequest::generateOrderReference();
        $redsysCharge->persist($orderReference);
        return view('redsys::app.index', [
            'orderReference'    => $orderReference,
            'price'             => $redsysCharge->price->format(),
            'orderId'           => $redsysCharge->orderId,
            'redsysConfig'      => $this->config,
            'customerToken'     => $customerToken,
            'cards'             => CardsTokenizable::get($customerToken)
        ])->render();
    }

    public function charge(RedsysCharge $redsysCharge, RedsysChargeRequest $chargeRequest) : ChargeResult
    {
        $operationId = $chargeRequest->operationId;
        $cardId      = $chargeRequest->cardId;
        if ($operationId == -1 || (! $operationId && ! $cardId)) {
            return new ChargeResult(false, "No operation Id");
        }
        return (new RedsysRequestInit($this->config))
            ->handle($chargeRequest, $redsysCharge->orderId, $redsysCharge->price);
    }

    public function refund(RedsysRefund $redsysRefund) : ChargeResult
    {
        return (new RedsysRequestRefund($this->config))
            ->handle($redsysRefund->paymentReference, $redsysRefund->price);
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
