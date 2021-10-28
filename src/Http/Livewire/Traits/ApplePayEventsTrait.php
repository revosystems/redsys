<?php

namespace Revosystems\Redsys\Http\Livewire\Traits;

use Revosystems\Redsys\Models\ChargeResult;

trait ApplePayEventsTrait
{
    protected $applePayListeners = [
        'onApplePayAuthorized'
    ];

    public function applePayListeners() : array
    {
        return $this->applePayListeners;
    }

    public function onApplePayAuthorized($data)
    {
//        $paymentHandler = LivewirePayHandler::fromSession();
//        $success        = $paymentHandler->prePayment();
//        if (! $success) {
//            return $this->emit('applePayPaymentCompleted', 'FAILED');
//        }
//        $gatewayResponse                 = app(WebAppPaymentGateway::class)->chargeWithApple($paymentHandler->getPaymentReference(), new Price($soloServices->order->total, $paymentHandler->store()->currency), $data);
//
//        $this->emit('applePayPaymentCompleted', $gatewayResponse->success ? 'SUCCESS' : 'FAILED');
//
//        $result         = $paymentHandler->onPaymentCompleted(new ChargeResult($gatewayResponse->success, $gatewayResponse, app(SoloServices::class)->order->total, $gatewayResponse->reference));
//        if (! $result->success) {
//            $paymentHandler->paymentError = $result->paymentError;
//            $paymentHandler->saveToSession();
//            return redirect($paymentHandler->paymentCompletedRedirectUrl(false));
//        }
//        return redirect($paymentHandler->paymentCompletedRedirectUrl(true));
    }
}
