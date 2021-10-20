<?php


namespace Revosystems\RedsysGateway;

use Revosystems\RedsysGateway\Lib\Model\Message\RESTRequestOperationMessage;
use Revosystems\RedsysGateway\Lib\Model\Message\RESTResponseMessage;
use Revosystems\RedsysGateway\Models\ChargeRequest;

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

    protected function requestOperation(ChargeRequest $data, $posOrderId, $amount, $currency) : RESTRequestOperationMessage
    {
        $operationMessageClass = $this->operationMessageClass();
        return (new $operationMessageClass)
            ->setMerchant($this->config->merchantCode)
            ->setTerminal($this->config->merchantTerminal)
            ->generate($data, $posOrderId, $amount, $currency);
    }

    protected function getResponse(RESTResponseMessage $response)
    {
        return [
            "result"    => $response->getResult(),
            "operation" => $response->getResult() !== 'KO' ? $response->getOperation() : null,
        ];
    }
}
