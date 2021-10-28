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

    public function __construct($class, $claveComercio)
    {
        $this->service = new $class($claveComercio, RedsysPaymentGateway::isTestEnvironment() ? RESTConstants::$ENV_SANDBOX : RESTConstants::$ENV_PRODUCTION);
    }

    public static function make($class, $key) : RedsysRest
    {
        return new self($class, $key);
    }

    public function sendOperation($request) : RESTResponseMessage
    {
        return $this->service->sendOperation($request);
    }
}
