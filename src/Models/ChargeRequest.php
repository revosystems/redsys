<?php

namespace Revosystems\Redsys\Models;

class ChargeRequest
{
    public $operationId;
    public $cardId;
    public $orderReference;
    public $customerToken;
    public $extraInfo;

    public function __construct($orderReference = null)
    {
        $this->orderReference       = $orderReference ?? static::generateOrderReference();
    }

    public static function makeWithCard($orderReference, string $cardId, array $extraInfo) : self
    {
        $chargeRequest = new self($orderReference);
        $chargeRequest->cardId          = $cardId;
        $chargeRequest->extraInfo       = $extraInfo;
        return $chargeRequest;
    }

    public static function makeWithOperationId($orderReference, string $operationId, array $extraInfo) : self
    {
        $chargeRequest = new self($orderReference);
        $chargeRequest->operationId     = $operationId;
        $chargeRequest->extraInfo       = $extraInfo;
        return $chargeRequest;
    }

    public static function generateOrderReference() : string
    {
        return substr(str_shuffle(str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789', 10)), 0, 12);
    }
}
