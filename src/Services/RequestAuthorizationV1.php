<?php


namespace Revosystems\Redsys\Services;

use Revosystems\Redsys\Lib\Model\Message\RESTAuthorizationRequestOperationMessage;
use Revosystems\Redsys\Lib\Utils\Price;
use Revosystems\Redsys\Models\RedsysConfig;

class RequestAuthorizationV1 extends RequestAuthorization
{
    public function __construct(RedsysConfig $config)
    {
        parent::__construct($config);
        $this->webhookHandler = new WebhookHandlerV1($config);
    }

    public function handle(RedsysChargePayment $chargePayment, RedsysChargeRequest $chargeRequest)
    {
        $requestOperation = (new RESTAuthorizationRequestOperationMessage)
            ->generate($this->config, $chargePayment, $chargeRequest)
            ->setCard($chargeRequest)
            ->setEMV3DSParamsV1();
        return $this->getAuthorizationChargeResult($chargePayment, $chargeRequest, $requestOperation);
    }
}
