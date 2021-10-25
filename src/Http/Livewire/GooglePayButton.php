<?php

namespace Revosystems\RedsysGateway\Http\Livewire;

use Livewire\Component;
use Revosystems\RedsysGateway\Http\Livewire\Traits\GooglePayEventsTrait;

class GooglePayButton extends Component
{
    use GooglePayEventsTrait;
    
    public function render()
    {
        return view('redsys-gateway::livewire.google-pay-button');
    }
}
