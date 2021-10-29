<?php

namespace Revosystems\Redsys\Models;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Revosystems\Redsys\Exceptions\SessionExpiredException;
use Revosystems\Redsys\Interfaces\RedsysOrder;

abstract class PaymentHandler
{
    const CACHE_KEY = 'redsys.handler.';

    public $order;
    public $account;

    abstract public function onPaymentSucceed(string $reference);
    abstract public function onPaymentFailed(?string $error = null);

    public function __construct(RedsysOrder $order, string $account)
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
}