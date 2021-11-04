<?php

namespace Revosystems\Redsys\Http\Livewire;

use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Revosystems\Redsys\Services\RedsysChargePayment;
use Revosystems\Redsys\Services\WebhookHandler;

class CheckStatus extends Component
{
    public $paymentReference;

    public function mount($paymentReference)
    {
        $this->paymentReference = $paymentReference;
    }

    public function render()
    {
        return view('redsys::livewire.check-status');
    }

    public function checkStatus()
    {
        if(! $result = Cache::get(WebhookHandler::ORDERS_CACHE_KEY . "{$this->paymentReference}.result")) {
            return;
        }
        if ($result === 'FAILED') {
            RedsysChargePayment::get($this->paymentReference)->payHandler->onPaymentFailed('Redsys payment failed');
            return;
        }
        if ($result === 'SUCCESS') {
            RedsysChargePayment::get($this->paymentReference)->payHandler->onPaymentSucceed($this->paymentReference);
        }
    }
}
