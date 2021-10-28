<?php

namespace Revosystems\Redsys\View\Components;

use Illuminate\View\Component;

class RadioSelector extends Component
{
    public $id;
    public $name;
    public $label;
    public $selected;
    public $hidden;
    public $hideInput;

    public function __construct(string $id, string $name, ?string $label = null, ?bool $selected = false, $hidden = false, $hideInput = false)
    {
        $this->id       = $id;
        $this->name     = $name;
        $this->label    = $label;
        $this->hidden   = $hidden;
        $this->selected = $selected;
        $this->hideInput= $hideInput;
    }

    public function render()
    {
        return view('redsys::components.radio-selector');
    }
}
