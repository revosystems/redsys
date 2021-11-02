<?php


namespace Revosystems\Redsys\Services;

use Revosystems\Redsys\Lib\Model\Message\RESTRequestOperationMessage;
use Revosystems\Redsys\Lib\Model\Message\RESTResponseMessage;
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

    protected function getResponse(RESTResponseMessage $response)
    {
        return [
            "result"    => $response->getResult(),
            "operation" => $response->getResult() !== 'KO' ? $response->getOperation() : null,
        ];
    }
}
