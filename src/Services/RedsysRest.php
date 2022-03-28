<?php


namespace Revosystems\Redsys\Services;

use Revosystems\Redsys\Lib\Constants\RESTConstants;
use Revosystems\Redsys\Lib\Model\Message\RESTResponseMessage;
use Revosystems\Redsys\Lib\Service\RESTService;
use Revosystems\Redsys\Models\RedsysPaymentGateway;

class RedsysRest
{
    /**
     * @var RESTService
     */
    private $service;

    public function __construct($class, $claveComercio, bool $test)
    {
        $this->service = new $class($claveComercio, $test ? RESTConstants::$ENV_SANDBOX : RESTConstants::$ENV_PRODUCTION);
    }

    public static function make($class, $key, bool $test) : RedsysRest
    {
        return new self($class, $key, $test);
    }

    public function sendOperation($request) : RESTResponseMessage
    {
        return $this->service->sendOperation($request);
    }
}
