<?php

namespace Revosystems\RedsysPayment\Models;

class ChargeResult
{
    public $success;
    /**
     * @var int|null
     */
    public $amount;
    public $reference;
    public $gatewayResponse;

    public function __construct($success, $gatewayResponse, $amount = null, $reference = null)
    {
        $this->success          = $success;
        $this->amount           = $amount;
        $this->reference        = $reference;
        $this->gatewayResponse  = $gatewayResponse;
    }
}