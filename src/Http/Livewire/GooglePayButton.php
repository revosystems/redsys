<?php

namespace Revosystems\Redsys\Http\Livewire;

use Livewire\Component;
use Revosystems\Redsys\Models\RedsysPaymentGateway;
use Revosystems\Redsys\Services\RedsysChargePayment;
use Revosystems\Redsys\Services\RedsysChargeRequest;

class GooglePayButton extends Component
{
    public $paymentReference;
    protected $listeners = [
        'onGooglePayAuthorized'
    ];

    public function mount(string $paymentReference)
    {
        $this->paymentReference = $paymentReference;
    }

    public function render()
    {
        return view('redsys::livewire.google-pay-button');
    }

    public function onGooglePayAuthorized($data)
    {
        $gatewayResponse = RedsysPaymentGateway::get()->chargeWithGoogle(
            RedsysChargePayment::get($this->paymentReference),
            (new RedsysChargeRequest($this->paymentReference)),
            $data);
        $this->emit('applePayPaymentCompleted', $gatewayResponse->success ? 'SUCCESS' : 'FAILED');
//        if (! $result->success) {
//            $paymentHandler->paymentError = $result->paymentError;
//            $paymentHandler->saveToSession();
//            return redirect($paymentHandler->paymentCompletedRedirectUrl(false));
//        }
//        return redirect($paymentHandler->paymentCompletedRedirectUrl(true));
    }

}
