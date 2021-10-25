<?php

namespace Revosystems\RedsysGateway\Models;

class ChargeRequest
{
    public $data;
    public $cardId;
    public $orderReference;
    public $shouldSaveCard;
    public $customerToken;
    public $extraInfo;

    public function __construct($data, $cardId, $orderReference, $shouldSaveCard = false, $customerToken = null, $extraInfo = null)
    {
        $this->data                 = $data;
        $this->cardId               = $cardId;
        $this->orderReference       = $orderReference;
        $this->shouldSaveCard       = $shouldSaveCard;
        $this->customerToken        = $customerToken;
        $this->extraInfo            = $extraInfo;
    }
}
