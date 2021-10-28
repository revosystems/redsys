<?php


namespace Revosystems\Redsys\Services;

use Revosystems\Redsys\Lib\Model\Message\RESTAuthorizationRequestOperationMessage;
use Revosystems\Redsys\Lib\Model\Message\RESTResponseMessage;
use Revosystems\Redsys\Models\ChargeRequest;

class RequestAuthorizationV2 extends RequestAuthorization
{
    public function handle(ChargeRequest $chargeRequest, $orderId, $amount, $currency, RESTResponseMessage $response)
    {
        $operationRequest = $this->requestOperation($chargeRequest, $orderId, $amount, $currency);
        $this->setEMV3DSParamsV2($chargeRequest, $operationRequest, $response, $orderId);
        return $this->getAuthorizationChargeResult($chargeRequest, $operationRequest, $orderId);
    }

    protected function setEMV3DSParamsV2(ChargeRequest $chargeRequest, RESTAuthorizationRequestOperationMessage $operationRequest, $response, $orderId): void
    {
        $threeDSInfo          = $response->getThreeDSInfo();
        $threeDSMethodURL     = $response->getThreeDSMethodURL();
        $protocolVersion      = $response->protocolVersionAnalysis();
        // TODO: REVIEW threeDSMethodUrl is not received with card 4918019199883839
        // TODO: only recevied with card: 4918019160034602 (frictionless). What should we do in this case?
        logger("BASIC DATA: {$threeDSInfo}, {$protocolVersion}, {$threeDSMethodURL}");
        if ($threeDSMethodURL) {
            // implementar nou flux
        }
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
            $this->getWebhookUrl($chargeRequest->orderReference, $orderId),
            $threeDSMethodURL ? 'N' : 'U'
        );
//        if (auth()->user()->email) {
//            $operationRequest->addEmvParameter(RESTConstants::$REQUEST_MERCHANT_EMV3DS_EMAIL, auth()->user()->email);
//        }
    }

    protected function getWebhookClass()
    {
        return WebhookV2::class;
    }
}
