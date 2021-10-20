<?php


namespace Revosystems\RedsysGateway;

class RedsysConfig
{
    public $merchantCode;
    public $merchantTerminal;
    public $claveComercio;
    public $test;

    public function __construct($merchantCode, $merchantTerminal, $claveComercio, $test = false)
    {
        $this->merchantCode     = $merchantCode ?? "999008881";
        $this->merchantTerminal = $merchantTerminal ?? "001";
        $this->claveComercio    = $claveComercio ?? "sq7HjrUOBfKmC576ILgskD5srU870gJ7";
        $this->test             = $test;
    }
}
