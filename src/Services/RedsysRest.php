<?php


namespace Revosystems\RedsysPayment\Services;

use Revosystems\RedsysPayment\Lib\Constants\RESTConstants;
use Revosystems\RedsysPayment\Lib\Model\Message\RESTResponseMessage;
use Revosystems\RedsysPayment\Lib\Service\RESTService;
use Revosystems\RedsysPayment\Models\Redsys;

class RedsysRest
{
    /**
     * @var RESTService
     */
    private $service;

    public function __construct($class, $claveComercio)
    {
        $this->service = new $class($claveComercio, Redsys::isTestEnvironment() ? RESTConstants::$ENV_SANDBOX : RESTConstants::$ENV_PRODUCTION);
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
