<?php


namespace Revosystems\Redsys\Services;

use Revosystems\Redsys\Lib\Model\Message\RESTRequestOperationMessage;
use Revosystems\Redsys\Lib\Model\Message\RESTResponseMessage;
use Revosystems\Redsys\Models\ChargeRequest;
use Revosystems\Redsys\Models\RedsysConfig;

abstract class RedsysRequest
{
    /**
     * @var RedsysConfig
     */
    protected $config;

    public function __construct(RedsysConfig $config)
    {
        $this->config = $config;
    }

    abstract protected function operationMessageClass();

    protected function requestOperation(ChargeRequest $chargeRequest, $orderId, $amount, $currency) : RESTRequestOperationMessage
    {
        $operationMessageClass = $this->operationMessageClass();
        return (new $operationMessageClass)
            ->setMerchant($this->config->code)
            ->setTerminal($this->config->terminal)
            ->generate($chargeRequest, $orderId, $amount, $currency);
    }

    protected function getResponse(RESTResponseMessage $response)
    {
        return [
            "result"    => $response->getResult(),
            "operation" => $response->getResult() !== 'KO' ? $response->getOperation() : null,
        ];
    }
}
