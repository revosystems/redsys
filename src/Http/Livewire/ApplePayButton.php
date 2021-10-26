<?php

namespace Revosystems\RedsysPayment\Http\Livewire;

use Livewire\Component;
use Revosystems\RedsysPayment\Http\Livewire\Traits\ApplePayEventsTrait;

class ApplePayButton extends Component
{
    use ApplePayEventsTrait;

    protected function getListeners(): array
    {
        return  $this->applePayListeners();
    }

    public function render()
    {
        return view('redsys-payment::livewire.apple-pay-button');
    }
}
