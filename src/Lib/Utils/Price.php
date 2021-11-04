<?php


namespace Revosystems\Redsys\Lib\Utils;

class Price
{
    public $amount;
    public $currency;

    public function __construct(int $amount, $code = 'EUR')
    {
        $this->amount   = $amount;
        $this->currency = new Currency($code);
    }

    public function format()
    {
        return $this->currency->symbol(number_format($this->amount / 100, 2, '.', ''));
    }
}