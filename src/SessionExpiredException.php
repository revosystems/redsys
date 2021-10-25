<?php


namespace Revosystems\RedsysGateway;

use RuntimeException;

class SessionExpiredException extends RuntimeException
{
    public function __construct()
    {
    }
}