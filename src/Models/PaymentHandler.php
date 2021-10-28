<?php

namespace Revosystems\RedsysPayment\Models;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Revosystems\RedsysPayment\Exceptions\SessionExpiredException;
use Revosystems\RedsysPayment\Interfaces\Order;

abstract class PaymentHandler
{
    const CACHE_KEY = 'rv-redsys-payment-gateway.handler.';

    public $order;
    public $account;

    abstract public function onPaymentCompleted(?string $error = null);
//    abstract public function onSuccess();
//    abstract public function onFailure();

    public function __construct(Order $order, string $account)
    {
        $this->account  = $account;
        $this->order    = $order;
    }

    public function persist(string $orderReference): self
    {
        Session::put(static::CACHE_KEY . $orderReference, serialize($this));
        return $this;
    }

    public static function get(string $orderReference) : self
    {
        if (! $handler = Session::get(static::CACHE_KEY . $orderReference)) {
            throw new SessionExpiredException();
        }
        return unserialize($handler);
    }

//    public function saveToCache(string $orderReference): void
//    {
//        Log::debug("[REDSYS] Serializing handler for order id {$orderReference} with value:" . json_encode($this));
//        Cache::put(static::CACHE_KEY . $orderReference, serialize($this), now()->addMinutes(30));
//    }
}