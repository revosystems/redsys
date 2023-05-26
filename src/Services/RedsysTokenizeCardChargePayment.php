<?php


namespace Revosystems\Redsys\Services;

use Revosystems\Redsys\Models\RedsysPaymentHandler;

class RedsysTokenizeCardChargePayment extends RedsysChargePayment
{

    public function __construct(RedsysPaymentHandler $payHandler, string $externalReference, string $tenant)
    {
        parent::__construct($payHandler, $externalReference, $tenant, 0, 'EUR');
    }

}
