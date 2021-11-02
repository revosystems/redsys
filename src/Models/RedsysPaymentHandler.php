<?php

namespace Revosystems\Redsys\Models;

interface RedsysPaymentHandler
{
    public function onPaymentSucceed(string $reference);
    public function onPaymentFailed(?string $error = null);
}