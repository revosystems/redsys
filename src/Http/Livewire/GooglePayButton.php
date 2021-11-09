<?php

namespace Revosystems\Redsys\Http\Livewire;

use Livewire\Component;
use Revosystems\Redsys\Models\RedsysPaymentGateway;
use Revosystems\Redsys\Services\RedsysChargePayment;
use Revosystems\Redsys\Services\RedsysChargeRequest;

class GooglePayButton extends Component
{
    public $paymentReference;
    public $merchantCode;
    public $amount;

    protected $listeners = [
        'onGooglePayAuthorized'
    ];

    public function mount(string $paymentReference, string $merchantCode, float $amount)
    {
        $this->paymentReference = $paymentReference;
        $this->merchantCode     = $merchantCode;
        $this->amount           = $amount;
    }

    public function render()
    {
        return view('redsys::livewire.google-pay-button');
    }

    public function onGooglePayAuthorized($data) : void
    {
        $gatewayResponse = RedsysPaymentGateway::get()->chargeWithGoogle(
            RedsysChargePayment::get($this->paymentReference),
            (new RedsysChargeRequest($this->paymentReference)),
            $data);
        if (! $gatewayResponse->success) {
            RedsysChargePayment::get($this->paymentReference)->payHandler->onPaymentFailed('Google pay error');
            return;
        }
        RedsysChargePayment::get($this->paymentReference)->payHandler->onPaymentSucceed($gatewayResponse->paymentReference);
    }
}
