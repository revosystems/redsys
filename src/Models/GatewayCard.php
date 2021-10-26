<?php


namespace Revosystems\RedsysPayment\Models;

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
}
