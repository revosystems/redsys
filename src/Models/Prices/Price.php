<?php

namespace Revosystems\RedsysPayment\Models\Prices;

class Price {
    // amount in min division. cents
    public $amount;
    public $currency;

    static public function fromArray($priceArray)
    {
        return new static($priceArray['amount'], $priceArray['code']);
    }

    public function __construct($amount = null, $code = 'EUR')
    {
        $this->amount = $amount;
        $this->currency = new Currency($code);
    }

    public function format()
    {
        return $this->currency->symbol($this->amount !== null ? number_format($this->amount / 100, 2, '.', '') : null);
    }

    public function to($currency)
    {
        if($this->currency->code == $currency){
            return $this;
        }

        $this->amount = $this->amount ? $this->amount * $this->currency->getExchangeRate() : $this->amount;
        $this->currency->code = $currency;

        return $this;
    }

    public function toArray()
    {
        return array_merge([
            'amount' => $this->amount
        ], $this->currency->toArray());
    }
}