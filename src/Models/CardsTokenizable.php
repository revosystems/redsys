<?php


namespace Revosystems\RedsysGateway\Models;

use Illuminate\Support\Collection;

interface CardsTokenizable
{
    public function getCardsForCustomer($customerToken) : Collection;
}
