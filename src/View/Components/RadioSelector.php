<?php

namespace Revosystems\RedsysPayment\View\Components;

use Illuminate\View\Component;

class RadioSelector extends Component
{
    public $id;
    public $name;
    public $label;
    public $selected;
    
    public function __construct(string $id, string $name, ?string $label = null, ?bool $selected = false)
    {
        $this->id       = $id;
        $this->name     = $name;
        $this->label    = $label;
        $this->selected = $selected;
    }

    public function render()
    {
        return view('redsys-payment::components.radio-selector');
    }
}
