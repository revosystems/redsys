<?php

namespace Revosystems\RedsysGateway\Http\Livewire;

use Livewire\Component;
use Revosystems\RedsysGateway\Http\Livewire\Traits\ApplePayEventsTrait;

class ApplePayButton extends Component
{
    use ApplePayEventsTrait;

    protected function getListeners(): array
    {
        return  $this->applePayListeners();
    }

    public function render()
    {
        return view('redsys-gateway::livewire.apple-pay-button');
    }
}
