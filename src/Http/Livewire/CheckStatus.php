<?php

namespace Revosystems\RedsysPayment\Http\Livewire;

use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Revosystems\RedsysPayment\Models\PaymentHandler;
use Revosystems\RedsysPayment\Models\RedsysPaymentGateway;
use Revosystems\RedsysPayment\Services\Webhook;

class CheckStatus extends Component
{
    public $orderReference;

    public function mount($orderReference)
    {
        $this->orderReference = $orderReference;
    }

    public function render()
    {
        return view('redsys-payment::livewire.check-status');
    }

    public function checkStatus()
    {
        if(! $result = Cache::get(Webhook::ORDERS_CACHE_KEY . "{$this->orderReference}.result")) {
            return;
        }
        if ($result === 'FAILED') {
            PaymentHandler::get($this->orderReference)->onPaymentCompleted('Redsys payment failed');
            return;
        }
        if ($result === 'SUCCESS') {
            PaymentHandler::get($this->orderReference)->onPaymentCompleted();
        }
    }
}
