<?php


namespace Revosystems\RedsysGateway;

use Revosystems\RedsysGateway\Lib\Constants\RESTConstants;
use Revosystems\RedsysGateway\Lib\Model\Message\RESTResponseMessage;
use Revosystems\RedsysGateway\Lib\Service\RESTService;

class RedsysRest
{
    /**
     * @var RESTService
     */
    private $service;

    public function __construct($class, $claveComercio, $test = false)
    {
        $this->service = new $class($claveComercio, $test ? RESTConstants::$ENV_SANDBOX : RESTConstants::$ENV_PRODUCTION);
    }

    public static function make($class, $claveComercio, $test = false): RedsysRest
    {
        return new self($class, $claveComercio, $test);
    }

    public function sendOperation($request) : RESTResponseMessage
    {
        return $this->service->sendOperation($request);
    }
}
