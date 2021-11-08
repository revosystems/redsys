<?php

namespace Revosystems\Redsys\Http\Livewire;

use Livewire\Component;
use Revosystems\Redsys\Models\RedsysPaymentGateway;
use Revosystems\Redsys\Services\RedsysChargePayment;
use Revosystems\Redsys\Services\RedsysChargeRequest;

class ApplePayButton extends Component
{
    public $paymentReference;
    public $tenant;
    public $amount;

    protected $listeners = [
        'onApplePayAuthorized'
    ];

    public function mount(string $paymentReference, string $tenant, float $amount)
    {
        $this->paymentReference = $paymentReference;
        $this->tenant = $tenant;
        $this->amount = $amount;
    }

    public function render()
    {
        return view('redsys::livewire.apple-pay-button');
    }

    public function onApplePayAuthorized($data)
    {
        $gatewayResponse = RedsysPaymentGateway::get()->chargeWithApple(
            RedsysChargePayment::get($this->paymentReference),
            (new RedsysChargeRequest($this->paymentReference)),
            $data);
        $this->emit('applePayPaymentCompleted', $gatewayResponse->success ? 'SUCCESS' : 'FAILED');

//        $result         = $paymentHandler->onPaymentCompleted(new ChargeResult($gatewayResponse->success, $gatewayResponse, app(SoloServices::class)->order->total, $gatewayResponse->reference));
//        if (! $result->success) {
//            $paymentHandler->paymentError = $result->paymentError;
//            $paymentHandler->saveToSession();
//            return redirect($paymentHandler->paymentCompletedRedirectUrl(false));
//        }
//        return redirect($paymentHandler->paymentCompletedRedirectUrl(true));
    }

    public function onPaymentCompleted() : void
    {
        RedsysChargePayment::get($this->paymentReference)->payHandler->onPaymentSucceed($this->paymentReference);
    }
}
