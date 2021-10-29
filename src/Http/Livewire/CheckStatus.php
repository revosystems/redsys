<?php

namespace Revosystems\Redsys\Http\Livewire;

use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Revosystems\Redsys\Models\PaymentHandler;
use Revosystems\Redsys\Models\RedsysPaymentGateway;
use Revosystems\Redsys\Services\Webhook;

class CheckStatus extends Component
{
    public $orderReference;

    public function mount($orderReference)
    {
        $this->orderReference = $orderReference;
    }

    public function render()
    {
        return view('redsys::livewire.check-status');
    }

    public function checkStatus()
    {
        if(! $result = Cache::get(Webhook::ORDERS_CACHE_KEY . "{$this->orderReference}.result")) {
            return;
        }
        if ($result === 'FAILED') {
            PaymentHandler::get($this->orderReference)->onPaymentFailed('Redsys payment failed');
            return;
        }
        if ($result === 'SUCCESS') {
            PaymentHandler::get($this->orderReference)->onPaymentSucceed($this->orderReference);
        }
    }
}
