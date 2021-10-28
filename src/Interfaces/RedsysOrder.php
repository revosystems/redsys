<?php


namespace Revosystems\Redsys\Interfaces;

use Revosystems\Redsys\Models\Prices\RedsysPrice;

Interface RedsysOrder
{
    public function price() : RedsysPrice;

    public function id() : string;
}