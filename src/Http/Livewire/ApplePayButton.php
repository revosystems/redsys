<?php

namespace Revosystems\Redsys\Http\Livewire;

use Livewire\Component;
use Revosystems\Redsys\Http\Livewire\Traits\ApplePayEventsTrait;

class ApplePayButton extends Component
{
    use ApplePayEventsTrait;

    protected function getListeners(): array
    {
        return  $this->applePayListeners();
    }

    public function render()
    {
        return view('redsys::livewire.apple-pay-button');
    }
}
