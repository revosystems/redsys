<?php


namespace Revosystems\Redsys\Lib\Utils;

class Price
{
    public $amount;
    public $currency;

    public function __construct($amount = null, $code = 'EUR')
    {
        $this->amount   = $amount;
        $this->currency = new Currency($code);
    }

    public function format()
    {
        return $this->currency->symbol($this->amount !== null ? number_format($this->amount / 100, 2, '.', '') : null);
    }
}