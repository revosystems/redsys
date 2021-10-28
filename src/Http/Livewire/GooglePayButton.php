<?php

namespace Revosystems\Redsys\Http\Livewire;

use Livewire\Component;
use Revosystems\Redsys\Http\Livewire\Traits\GooglePayEventsTrait;

class GooglePayButton extends Component
{
    use GooglePayEventsTrait;
    
    public function render()
    {
        return view('redsys::livewire.google-pay-button');
    }
}
