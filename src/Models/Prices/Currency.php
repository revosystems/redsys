<?php

namespace Revosystems\RedsysPayment\Models\Prices;

class Currency {
    public $code;

    public function __construct($code)
    {
        $this->code = $code;
    }

    public function symbol($value = null)
    {
        $symbols = include(__DIR__. '/../../resources/currencies/currency_symbols.php');
        $symbol = $symbols[$this->code] ?? "";

        return $value ? ($this->isSymbolBeforeValue() ? $symbol.' '.$value : $value.' '.$symbol) : $symbol;
    }

    public function isSymbolBeforeValue()
    {
        return $this->code == 'USD'|| $this->code == 'GBP';
    }

    public function toArray()
    {
        return [
          'code' => $this->code,
          'symbol' => $this->symbol(),  
        ];
    }

    public function numericCode()
    {
        $symbols = include(__DIR__. '/../../resources/currencies/currency_codes.php');
        return $symbols[$this->code] ?? "";
    }
}