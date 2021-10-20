<?php

namespace Revosystems\RedsysGateway;

use Revosystems\RedsysGateway\Models\ChargeResult;
use Revosystems\RedsysGateway\Lib\Constants\RESTConstants;
use Revosystems\RedsysGateway\Lib\Model\Message\RESTAuthorizationRequestOperationMessage;
use Revosystems\RedsysGateway\Lib\Service\Impl\RESTTrataRequestService;
use Revosystems\RedsysGateway\Models\ChargeRequest;
use Illuminate\Support\Facades\Log;

abstract class RedsysRequestAuthorization extends RedsysRequest
{
    abstract protected function getWebhookClass();
    protected function operationMessageClass()
    {
        return RESTAuthorizationRequestOperationMessage::class;
    }

    protected function getAuthorizationChargeResult(ChargeRequest $data, RESTAuthorizationRequestOperationMessage $operationRequest, $posOrderId): ChargeResult
    {
        if ($data->shouldSaveCard) {
            $operationRequest->createReference();
        }

        $response = RedsysRest::make(RESTTrataRequestService::class, $this->config->claveComercio, $this->config->test)->sendOperation($operationRequest);
        $result   = $response->getResult();
        Log::debug("[REDSYS] Getting response {$result}");
        if ($result == RESTConstants::$RESP_LITERAL_KO) {
            Log::error("[REDSYS] Operation authorization was not OK");
            return new ChargeResult(false, $this->getResponse($response));
        }
        $operation = $response->getOperation();
        if ($data->shouldSaveCard && $operation->getMerchantIdentifier()) {
            Redsys::tokenizeCards($operation, $data->customerToken);
        }
        if ($result == RESTConstants::$RESP_LITERAL_OK) {
            Log::debug("[REDSYS] Operation authorization was OK (frictionless)");
            return new ChargeResult(true, $this->getResponse($response), $operationRequest->getAmount(), "redsys:{$data->orderId}");
        }
        return (new RedsysChallengeForm($this->redsysWebhook()))->display($data, $response, $this->getWebhookUrl($data->orderId, $posOrderId), $operationRequest->getAmount());
    }

    protected function redsysWebhook()
    {
        $webhookClass = $this->getWebhookClass();
        return new $webhookClass($this->config);
    }

    protected function getWebhookUrl($orderIdentifier, $posOrderId) : string
    {
        return route('webhooks.redsys', compact('orderIdentifier', 'posOrderId'));
    }
}
