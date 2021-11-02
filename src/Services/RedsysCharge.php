<?php


namespace Revosystems\Redsys\Services;

use Illuminate\Support\Facades\Session;
use Revosystems\Redsys\Exceptions\SessionExpiredException;
use Revosystems\Redsys\Lib\Utils\Price;
use Revosystems\Redsys\Models\RedsysPaymentHandler;

class RedsysCharge
{
    const CACHE_KEY = 'redsys.handler.';

    public $payHandler;
    public $orderId;
    public $price;
    public $tenant;

    public function __construct(RedsysPaymentHandler $payHandler, string $orderId, int $amount, string $currency, string $tenant)
    {
        $this->payHandler   = $payHandler;
        $this->orderId      = $orderId;
        $this->tenant       = $tenant;
        $this->price        = new Price($amount, $currency);
    }

    //==================================
    // METHODS TO PERSIST SECTION
    //==================================
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
