<?php

namespace Revosystems\RedsysPayment\Http\Livewire;

use Illuminate\Support\Facades\Cache;
use Livewire\Component;

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
        $result = Cache::get("rv-redsys-payment.webhooks.{$this->orderReference}.result");
        logger('result status');
        logger($result);
        if ($result == 'FAILED') {
            return $this->emit('onPaymentCompleted', "Redsys payment failed");
        }
        if ($result == 'SUCCESS') {
            return $this->emit('onPaymentCompleted');
        }
    }
}
