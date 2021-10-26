<?php


namespace Revosystems\RedsysPayment\Models;

use Illuminate\Support\Collection;

interface CardsTokenizable
{
    public function getCardsForCustomer($customerToken) : Collection;
}
