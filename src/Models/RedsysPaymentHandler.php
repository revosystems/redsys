<?php

namespace Revosystems\Redsys\Models;

interface RedsysPaymentHandler
{
    public function onPaymentSucceed(string $reference, ?ChargeResult $result = null);
    public function onPaymentFailed(?string $error = null);
}