<?php


namespace Revosystems\Redsys\Services;

use Revosystems\Redsys\Lib\Model\Message\RESTAuthorizationRequestOperationMessage;
use Revosystems\Redsys\Lib\Model\Message\RESTRequestOperationMessage;
use Revosystems\Redsys\Lib\Model\Message\RESTResponseMessage;
use Revosystems\Redsys\Lib\Utils\Price;
use Revosystems\Redsys\Models\ChargeResult;
use Revosystems\Redsys\Models\RedsysConfig;

class RequestAuthorizationV2 extends RequestAuthorization
{
    public function __construct(RedsysConfig $config)
    {
        parent::__construct($config);
        $this->webhookHandler = new WebhookHandlerV2($config);
    }

    public function handle(RedsysChargePayment $chargePayment, RedsysChargeRequest $chargeRequest, RESTResponseMessage $response) : ChargeResult
    {
        $requestOperation = (new RESTAuthorizationRequestOperationMessage)
            ->generate($this->config, $chargePayment, $chargeRequest)
            ->setCard($chargeRequest);
        $this->setEMV3DSParamsV2($chargePayment, $chargeRequest, $requestOperation, $response);
        return $this->getAuthorizationChargeResult($chargePayment, $chargeRequest, $requestOperation);
    }

    protected function setEMV3DSParamsV2(RedsysChargePayment $chargePayment, RedsysChargeRequest $chargeRequest, RESTRequestOperationMessage $operationRequest, RESTResponseMessage $response): void
    {
        $threeDSInfo          = $response->getThreeDSInfo();
        $threeDSMethodURL     = $response->getThreeDSMethodURL();
        $protocolVersion      = $response->protocolVersionAnalysis();
        // TODO: REVIEW threeDSMethodUrl is not received with card 4918019199883839
        // TODO: only recevied with card: 4918019160034602 (frictionless). What should we do in this case?
//        Log::debug("BASIC DATA: {$threeDSInfo}, {$protocolVersion}, {$threeDSMethodURL}");
        if ($threeDSMethodURL) {
            // implementar nou flux
        }
        if ($chargeRequest->extraInfo){
            $operationRequest->setEMV3DSParamsV2(
                $protocolVersion,
                request()->header('Accept'),
                request()->header('User-Agent'),
                "true", "true",
                request()->ip(),
    //            request()->header('Accept-Language', 'es-ES'), // navigator.language
                "es-ES",
                (string)$chargeRequest->extraInfo['browser_color_depth'],
                (string)$chargeRequest->extraInfo['browser_height'],
                (string)$chargeRequest->extraInfo['browser_width'],
                (string)$chargeRequest->extraInfo['browser_tz'],
                $response->getThreeDSServerTransID(),
                $this->getWebhookUrl($chargePayment, $chargeRequest->paymentReference),
                $threeDSMethodURL ? 'N' : 'U'
            );
        }
//        if (auth()->user()->email) {
//            $operationRequest->addEmvParameter(RESTConstants::$REQUEST_MERCHANT_EMV3DS_EMAIL, auth()->user()->email);
//        }
    }
}
