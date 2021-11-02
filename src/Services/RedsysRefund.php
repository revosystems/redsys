<?php


namespace Revosystems\Redsys\Services;

use Revosystems\Redsys\Lib\Utils\Price;

class RedsysRefund
{
    public $paymentReference;
    public $price;

    public function __construct(string $paymentReference, int $amount, string $currency)
    {
        $this->paymentReference = $paymentReference;
        $this->price            = new Price($amount, $currency);
    }
}
