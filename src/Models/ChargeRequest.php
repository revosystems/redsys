<?php

namespace Revosystems\RedsysGateway\Models;

class ChargeRequest
{
    public $data;
    public $cardId;
    public $orderId;
    public $shouldSaveCard;
    public $customerToken;
    public $extraInfo;

    public function __construct($data, $cardId, $orderId, $shouldSaveCard = false, $customerToken = null, $extraInfo = null)
    {
        $this->data             = $data;
        $this->cardId           = $cardId;
        $this->orderId          = $orderId;
        $this->shouldSaveCard   = $shouldSaveCard;
        $this->customerToken    = $customerToken;
        $this->extraInfo = $extraInfo;
    }
}
