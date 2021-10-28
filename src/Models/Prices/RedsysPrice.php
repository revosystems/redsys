<?php

namespace Revosystems\Redsys\Models\Prices;

interface RedsysPrice
{
    public function __construct($amount = null, $code = 'EUR');
    public function format();
}
