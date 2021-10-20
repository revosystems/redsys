<?php

namespace App\Services\Redsys\Lib\Service\Impl;

use App\Services\Redsys\Lib\Constants\RESTConstants;
use App\Services\Redsys\Lib\Model\Message\RESTResponseMessage;
use App\Services\Redsys\Lib\Service\RESTService;

class RESTInitialRequestService extends RESTService
{
    protected $operation = "0"; // RESTConstants::$INICIA;

    protected function parseResponse(RESTResponseMessage $response, $paramsB64, $trataPeticionResponse): RESTResponseMessage
    {
        if (! $this->checkSignature($paramsB64, $response->getOperation()->getSignature())) {
            return $response->setResult(RESTConstants::$RESP_LITERAL_KO);
        }
        $responseCode = $response->getOperation()->getResponseCode();
        if ($responseCode == null && $response->getOperation()->getPsd2() != null && $response->getOperation()->getPsd2() == RESTConstants::$RESPONSE_PSD2_TRUE) {
            return $response->setResult(RESTConstants::$RESP_LITERAL_AUT);
        }
        if ($responseCode == null && $response->getOperation()->getPsd2() != null && $response->getOperation()->getPsd2() == RESTConstants::$RESPONSE_PSD2_FALSE) {
            return $response->setResult(RESTConstants::$RESP_LITERAL_OK);
        }
        return $response->setResult(RESTConstants::$RESP_LITERAL_KO);
    }
}
