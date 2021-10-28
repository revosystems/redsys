<?php


namespace Revosystems\Redsys\Models;

use Revosystems\Redsys\Lib\Model\Element\RESTOperationElement;

class GatewayCard
{
    public $id;
    public $alias;
    public $expiration;

    public function __construct($id, $alias, $expiration)
    {
        $this->id           = $id;
        $this->alias        = $alias;
        $this->expiration   = $expiration;
    }

    public static function makeFromOperation(RESTOperationElement $operation) : self
    {
        return new GatewayCard($operation->getMerchantIdentifier(), $operation->getCardNumber(), $operation->getExpiryDate());
    }
}
