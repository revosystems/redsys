<?php


namespace Revosystems\RedsysPayment\Interfaces;

use Revosystems\RedsysPayment\Models\Prices\Price;

Interface Order
{
    public function price() : Price;

    public function id() : string;
}