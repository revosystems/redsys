<?php


namespace Revosystems\Redsys\Models;

class RedsysConfig
{
    public $code;
    public $terminal;
    public $key;
    public $test;
    public $refundPolicy;
    public $legalInfo;
    public $applePayEnabled;
    public $googlePayEnabled;

    public function __construct(array $config, bool $test = false)
    {
        $this->code     = $config['merchant_code'] ?? "999008881";
        $this->key      = $config['merchant_key'] ?? "sq7HjrUOBfKmC576ILgskD5srU870gJ7";
        $this->terminal = $config['merchant_terminal'] ?? "001";
        $this->applePayEnabled = $config['apple_pay_enabled'] ?? true; // we set the default value to true for consistency with the previous behaviour
        $this->googlePayEnabled = $config['google_pay_enabled'] ?? true;
        $this->legalInfo = $config['legal_info'] ?? null;
        $this->refundPolicy = $config['refund_policy'] ?? null;
        $this->test = config('services.payment_gateways.redsys.test') || $test;
    }
}
