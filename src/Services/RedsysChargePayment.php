<?php


namespace Revosystems\Redsys\Services;

use Illuminate\Support\Facades\Session;
use Revosystems\Redsys\Exceptions\SessionExpiredException;
use Revosystems\Redsys\Lib\Utils\Price;
use Revosystems\Redsys\Models\RedsysPaymentHandler;

class RedsysChargePayment extends RedsysPayment
{
    const CACHE_KEY = 'redsys.handler.';

    public $payHandler;

    public function __construct(RedsysPaymentHandler $payHandler, string $externalReference, string $tenant, int $amount, string $currency)
    {
        parent::__construct($externalReference, $tenant, $amount, $currency);
        $this->payHandler   = $payHandler;
    }

    //==================================
    // METHODS TO PERSIST SECTION
    //==================================
    public function persist(string $paymentReference): self
    {
        Session::put(static::CACHE_KEY . $paymentReference, serialize($this));
        return $this;
    }

    public static function get(string $paymentReference) : self
    {
        if (! $handler = Session::get(static::CACHE_KEY . $paymentReference)) {
            throw new SessionExpiredException();
        }
        return unserialize($handler);
    }
}
