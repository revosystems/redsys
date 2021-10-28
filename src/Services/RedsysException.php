<?php


namespace Revosystems\Redsys\Services;

class RedsysException extends \RuntimeException
{
    /**
     * RedsysException constructor.
     * @param string $string
     */
    public function __construct(string $string)
    {
        parent::__construct($string);
    }
}
