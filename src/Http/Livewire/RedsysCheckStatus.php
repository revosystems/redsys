<?php

namespace Revosystems\RedsysGateway\Http\Livewire;

use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class RedsysCheckStatus extends Component
{
    public $orderId;

    public function mount($orderId)
    {
        $this->orderId = $orderId;
    }

    public function render()
    {
        return view('livewire.redsys-check-status');
    }

    public function checkStatus()
    {
        $result = Cache::get("redsys.webhooks.{$this->orderId}.result");
        if ($result == 'FAILED') {
            return $this->emit('onPaymentCompleted', "Redsys payment failed");
        }
        if ($result == 'SUCCESS') {
            return $this->emit('onPaymentCompleted');
        }
    }
}
