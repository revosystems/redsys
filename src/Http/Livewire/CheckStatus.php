<?php

namespace Revosystems\Redsys\Http\Livewire;

use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Revosystems\Redsys\Services\RedsysCharge;
use Revosystems\Redsys\Services\WebhookHandler;

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
        if(! $result = Cache::get(WebhookHandler::ORDERS_CACHE_KEY . "{$this->orderReference}.result")) {
            return;
        }
        if ($result === 'FAILED') {
            RedsysCharge::get($this->orderReference)->payHandler->onPaymentFailed('Redsys payment failed');
            return;
        }
        if ($result === 'SUCCESS') {
            RedsysCharge::get($this->orderReference)->payHandler->onPaymentSucceed($this->orderReference);
        }
    }
}
