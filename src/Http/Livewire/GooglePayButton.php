<?php

namespace Revosystems\RedsysPayment\Http\Livewire;

use Livewire\Component;
use Revosystems\RedsysPayment\Http\Livewire\Traits\GooglePayEventsTrait;

class GooglePayButton extends Component
{
    use GooglePayEventsTrait;
    
    public function render()
    {
        return view('redsys-payment::livewire.google-pay-button');
    }
}
