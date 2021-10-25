<?php

namespace Revosystems\RedsysGateway\Http\Livewire;

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
        return view('redsys-gateway::livewire.check-status');
    }

    public function checkStatus()
    {
        $result = Cache::get("redsys.webhooks.{$this->orderReference}.result");
        if ($result == 'FAILED') {
            return $this->emit('onPaymentCompleted', "Redsys payment failed");
        }
        if ($result == 'SUCCESS') {
            return $this->emit('onPaymentCompleted');
        }
    }
}
