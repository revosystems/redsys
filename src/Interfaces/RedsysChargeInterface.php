<?php

namespace Revosystems\RedsysGateway\Interfaces;

interface RedsysChargeInterface
{
    public function order() : Order;
    public function account() : string;
    public function total() : int;
    public function onSuccess();
    public function onFailure();
}