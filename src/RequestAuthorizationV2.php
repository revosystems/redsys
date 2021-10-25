<?php


namespace Revosystems\RedsysGateway;

use Revosystems\RedsysGateway\Lib\Model\Message\RESTAuthorizationRequestOperationMessage;
use Revosystems\RedsysGateway\Lib\Model\Message\RESTResponseMessage;
use Revosystems\RedsysGateway\Models\ChargeRequest;

class RequestAuthorizationV2 extends RequestAuthorization
{
    public function handle(ChargeRequest $data, $posOrderId, $amount, $currency, RESTResponseMessage $response)
    {
        $operationRequest = $this->requestOperation($data, $posOrderId, $amount, $currency);
        $this->setEMV3DSParamsV2($data, $operationRequest, $response, $posOrderId);
        return $this->getAuthorizationChargeResult($data, $operationRequest, $posOrderId);
    }

    protected function setEMV3DSParamsV2(ChargeRequest $data, RESTAuthorizationRequestOperationMessage $operationRequest, $response, $posOrderId): void
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
            (string)$data->extraInfo['browser_color_depth'],
            (string)$data->extraInfo['browser_height'],
            (string)$data->extraInfo['browser_width'],
            (string)$data->extraInfo['browser_tz'],
            $response->getThreeDSServerTransID(),
            $this->getWebhookUrl($data->orderReference, $posOrderId),
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
