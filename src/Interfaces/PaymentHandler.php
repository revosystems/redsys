<?php

namespace Revosystems\RedsysPayment\Interfaces;

interface PaymentHandler
{
    public function order() : Order;
    public function account(): string;
    public function onPaymentCompleted($result);
    public function onSuccess();
    public function onFailure();

    public function persist(string $orderReference) : self;
    public static function get(string $orderReference) : self;
}