<?php


namespace Revosystems\Redsys\Models;

use Illuminate\Support\Facades\Session;
use Revosystems\Redsys\Exceptions\SessionExpiredException;
use Revosystems\Redsys\Services\RedsysChargePayment;
use Revosystems\Redsys\Services\RedsysChargeRequest;
use Revosystems\Redsys\Services\RedsysPayment;
use Revosystems\Redsys\Services\RedsysRequestInit;
use Revosystems\Redsys\Services\RedsysRequestRefund;

class RedsysPaymentGateway
{
    const PERSIST_KET = 'redsys.payment-gateway';

    /**
     * @var RedsysConfig
     */
    protected $config;

    public function __construct(RedsysConfig $config)
    {
        $this->config = $config;
    }

    public static function make(RedsysConfig $config) : self
    {
        return (new self($config))->persist();
    }

    public static function isTestEnvironment() : bool
    {
        return config('services.payment_gateways.redsys.test');
    }

    public function render(RedsysChargePayment $chargePayment, $customerToken)
    {
        $paymentReference = RedsysChargeRequest::generatePaymentReference();
        $chargePayment->persist($paymentReference);
        return view('redsys::app.index', [
            'paymentReference'  => $paymentReference,
            'price'             => $chargePayment->price->format(),
            'externalReference' => $chargePayment->externalReference,
            'redsysConfig'      => $this->config,
            'customerToken'     => $customerToken,
            'cards'             => CardsTokenizable::get($customerToken)
        ])->render();
    }

    public function charge(RedsysChargePayment $chargePayment, RedsysChargeRequest $chargeRequest) : ChargeResult
    {
        $operationId = $chargeRequest->operationId;
        $cardId      = $chargeRequest->cardId;
        if ($operationId === -1 || (! $operationId && ! $cardId)) {
            return new ChargeResult(false);
        }
        return (new RedsysRequestInit($this->config))->handle($chargePayment, $chargeRequest);
    }

    public function refund(RedsysPayment $chargePayment, RedsysChargeRequest $chargeRequest) : ChargeResult
    {
        return (new RedsysRequestRefund($this->config))->handle($chargePayment, $chargeRequest);
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
