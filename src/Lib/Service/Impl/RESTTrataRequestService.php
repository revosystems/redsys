<?php

namespace Revosystems\RedsysPayment\Lib\Service\Impl;

use Revosystems\RedsysPayment\Lib\Constants\RESTConstants;
use Revosystems\RedsysPayment\Lib\Model\Message\RESTResponseMessage;
use Revosystems\RedsysPayment\Lib\Service\RESTService;

class RESTTrataRequestService extends RESTService
{
    protected $operation = "1"; // RESTConstants::$TRATA;

    protected function parseResponse(RESTResponseMessage $response, $paramsB64, $trataPeticionResponse): RESTResponseMessage
    {
        if (! $this->checkSignature($paramsB64, $response->getOperation()->getSignature())) {
            return $response->setResult(RESTConstants::$RESP_LITERAL_KO);
        }
        if ($response->getOperation()->requiresSCA()) {
            return $response->setResult(RESTConstants::$RESP_LITERAL_AUT);
        }
        $transType    = $response->getTransactionType();
        $responseCode = (int)$response->getOperation()->getResponseCode();

        if ($responseCode == RESTConstants::$AUTHORIZATION_OK) {
            return $response->setResult(($transType == RESTConstants::$AUTHORIZATION || $transType == RESTConstants::$PREAUTHORIZATION) ? RESTConstants::$RESP_LITERAL_OK : RESTConstants::$RESP_LITERAL_KO);
        }
        if ($responseCode == RESTConstants::$CONFIRMATION_OK) {
            return $response->setResult(($transType == RESTConstants::$CONFIRMATION || $transType == RESTConstants::$REFUND) ? RESTConstants::$RESP_LITERAL_OK : RESTConstants::$RESP_LITERAL_KO);
        }
        if ($responseCode == RESTConstants::$CANCELLATION_OK) {
            return $response->setResult($transType == RESTConstants::$CANCELLATION ? RESTConstants::$RESP_LITERAL_OK : RESTConstants::$RESP_LITERAL_KO);
        }
        return $response->setResult(RESTConstants::$RESP_LITERAL_KO);
    }
}
