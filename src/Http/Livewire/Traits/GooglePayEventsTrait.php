<?php

namespace Revosystems\Redsys\Http\Livewire\Traits;

trait GooglePayEventsTrait
{
    protected $googlePayListeners = [
        'onGooglePayAuthorized'
    ];

    function googlePayListeners() : array {
        return $this->googlePayListeners;
    }


    public function onGooglePayAuthorized($data)
    {
//        $soloServices   = app(SoloServices::class);
//        $paymentHandler = LivewirePayHandler::fromSession();
//        $success        = $paymentHandler->prePayment();
//        if (! $success) {
//            return redirect($paymentHandler->paymentCompletedRedirectUrl(false));
//        }
//        $gatewayResponse        = app(WebAppPaymentGateway::class)->chargeWithGoogle($paymentHandler->getPaymentReference(), new Price($soloServices->order->total, $paymentHandler->store()->currency), $data);
//        $result         = $paymentHandler->onPaymentCompleted(new ChargeResult($gatewayResponse->success, $gatewayResponse, app(SoloServices::class)->order->total, $gatewayResponse->reference));
//        if (! $result->success) {
//            $paymentHandler->paymentError = $result->paymentError;
//            $paymentHandler->saveToSession();
//            return redirect($paymentHandler->paymentCompletedRedirectUrl(false));
//        }
//        return redirect($paymentHandler->paymentCompletedRedirectUrl(true));
    }
}
