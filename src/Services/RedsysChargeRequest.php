<?php

namespace Revosystems\Redsys\Services;

class RedsysChargeRequest
{
    public $operationId;
    public $cardId;
    public $paymentReference;
    public $customerToken;
    public $extraInfo;

    public function __construct(?string $paymentReference = null)
    {
        $this->paymentReference = $paymentReference ?? static::generatePaymentReference();
    }

    public static function makeWithCard($paymentReference, string $cardId, array $extraInfo) : self
    {
        $chargeRequest = new self($paymentReference);
        $chargeRequest->cardId          = $cardId;
        $chargeRequest->extraInfo       = $extraInfo;
        return $chargeRequest;
    }

    public static function makeWithOperationId($paymentReference, string $operationId, array $extraInfo) : self
    {
        $chargeRequest = new self($paymentReference);
        $chargeRequest->operationId     = $operationId;
        $chargeRequest->extraInfo       = $extraInfo;
        return $chargeRequest;
    }

    public static function generatePaymentReference() : string
    {
        return substr(str_shuffle(str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789', 10)), 0, 12);
    }
}
