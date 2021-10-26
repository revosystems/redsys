<?php


namespace Revosystems\RedsysPayment\Exceptions;

use RuntimeException;

class SessionExpiredException extends RuntimeException
{
    public function __construct()
    {
    }
}