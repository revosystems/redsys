<?php

namespace Revosystems\RedsysPayment\Models;

class ChargeRequest
{
    public $idOper;
    public $cardId;
    public $orderReference;
    public $shouldSaveCard;
    public $customerToken;
    public $extraInfo;

    public function __construct($orderReference = null)
    {
        $this->orderReference       = $orderReference ?? static::generateOrderReference();
    }

    public static function make($orderReference, ?string $idOper, ?string $cardId, bool $shouldSaveCard, ?string $customerToken, array $extraInfo) : self
    {
        $chargeRequest = new self($orderReference);
        $chargeRequest->idOper          = $idOper;
        $chargeRequest->cardId          = $cardId;
        $chargeRequest->shouldSaveCard  = $shouldSaveCard;
        $chargeRequest->customerToken   = $customerToken;
        $chargeRequest->extraInfo       = $extraInfo;
        return $chargeRequest;
    }

    public static function generateOrderReference() : string
    {
        return substr(str_shuffle(str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789', 10)), 0, 12);
    }
}
