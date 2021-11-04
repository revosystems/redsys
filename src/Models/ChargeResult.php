<?php

namespace Revosystems\Redsys\Models;

class ChargeResult
{
    public $success;
    public $paymentReference;
    public $gatewayResponse;

    public function __construct(bool $success, ?array $gatewayResponse = null, ?string $paymentReference = null)
    {
        $this->success          = $success;
        $this->paymentReference = $paymentReference;
        $this->gatewayResponse  = $gatewayResponse;
    }
}
