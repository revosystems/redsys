<?php


namespace Revosystems\Redsys\Models;

class RedsysConfig
{
    public $code;
    public $terminal;
    public $key;
    public $test;

    public function __construct(array $config, bool $test = false)
    {
        $this->code     = $config['merchant_code'] ?? "999008881";
        $this->key      = $config['merchant_key'] ?? "sq7HjrUOBfKmC576ILgskD5srU870gJ7";
        $this->terminal = $config['merchant_terminal'] ?? "001";
        $this->test = $test;
    }
}
