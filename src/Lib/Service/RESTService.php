<?php

namespace Revosystems\RedsysPayment\Lib\Service;

use Revosystems\RedsysPayment\Lib\Constants\RESTConstants;
use Revosystems\RedsysPayment\Lib\Model\Element\RESTOperationElement;
use Revosystems\RedsysPayment\Lib\Model\Message\RESTInitialRequestOperationMessage;
use Revosystems\RedsysPayment\Lib\Model\Message\RESTResponseMessage;
use Revosystems\RedsysPayment\Lib\Utils\RESTSignatureUtils;
use Illuminate\Support\Facades\Log;

abstract class RESTService
{
    private $signatureKey;
    private $env;
    protected $operation;

    public function __construct($signatureKey, $env)
    {
        $this->signatureKey       = $signatureKey;
        $this->env                = $env;
    }

    public function sendOperation($message)
    {
        $result         = "";
        $post_request   = $this->createRequestSOAPMessage($message);
        $header         = [
            "Cache-Control: no-cache",
            "Pragma: no-cache",
            "Content-length: " . strlen($post_request)
        ];
        $url_rs = RESTConstants::getEnviromentEndpoint($this->env, $this->operation);

        $rest_do = curl_init();
        curl_setopt($rest_do, CURLOPT_URL, $url_rs);
        curl_setopt($rest_do, CURLOPT_CONNECTTIMEOUT, RESTConstants::$CONNECTION_TIMEOUT_VALUE);
        curl_setopt($rest_do, CURLOPT_TIMEOUT, RESTConstants::$READ_TIMEOUT_VALUE);
        curl_setopt($rest_do, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($rest_do, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($rest_do, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($rest_do, CURLOPT_SSLVERSION, RESTConstants::$SSL_TLSv12);
        curl_setopt($rest_do, CURLOPT_POST, true);
        curl_setopt($rest_do, CURLOPT_POSTFIELDS, $post_request);
        curl_setopt($rest_do, CURLOPT_HTTPHEADER, $header);

        $tmp        = curl_exec($rest_do);
        $httpCode   = curl_getinfo($rest_do, CURLINFO_HTTP_CODE);

        if ($tmp !== false && $httpCode == 200) {
            $result = $tmp;
        } else {
            Log::error("[REDSYS] Request failure: " . (($httpCode != 200) ? "[HttpCode: '{$httpCode}']" : "") . ((curl_error($rest_do)) ? " [Error: '".curl_error($rest_do)."']" : ""));
        }

        curl_close($rest_do);
        return $this->createResponseMessage($result);
    }

    public function createRequestSOAPMessage($message)
    {
        $request        = $this->createRequestMessage($message);
        return http_build_query([
            "Ds_MerchantParameters" => $request->getDatosEntradaB64(),
            "Ds_SignatureVersion"   => $request->getSignatureVersion(),
            "Ds_Signature"          => $request->getSignature()
        ]);
    }

    public function createRequestMessage($message)
    {
        return tap((new RESTInitialRequestOperationMessage), function (RESTInitialRequestOperationMessage $requestMessage) use ($message) {
            $requestMessage->setDatosEntrada($message);
            $requestMessage->setSignature(RESTSignatureUtils::createMerchantSignature($this->getSignatureKey(), $requestMessage->getDatosEntradaB64()));
        });
    }

    public function createResponseMessage($trataPeticionResponse)
    {
        $varArray = json_decode($trataPeticionResponse, true);
        if (isset($varArray["ERROR"]) || isset($varArray["errorCode"])) {
            Log::error("[REDSYS] Received JSON: '{$trataPeticionResponse}'");
            return (new RESTResponseMessage)->setResult(RESTConstants::$RESP_LITERAL_KO);
        }
        $response = $this->unMarshallResponseMessage($varArray);
        $paramsB64 = $varArray["Ds_MerchantParameters"];

        return $this->parseResponse($response, $paramsB64, $trataPeticionResponse);
    }

    public function unMarshallResponseMessage($messageArray)
    {
        $operation  = new RESTOperationElement;
        $operation->parseJson(base64_decode($messageArray["Ds_MerchantParameters"]));
        $operation->setSignature($messageArray["Ds_Signature"]);
        return (new RESTResponseMessage)->setOperation($operation);
    }

    abstract protected function parseResponse(RESTResponseMessage $response, $paramsB64, $trataPeticionResponse): RESTResponseMessage;

    protected function checkSignature($sentData, $remoteSignature)
    {
        $calcSignature = RESTSignatureUtils::createMerchantSignatureNotif($this->getSignatureKey(), $sentData);
        if ($remoteSignature != $calcSignature) {
            logger("Signature doesnt match: '{$remoteSignature}' <> '{$calcSignature}'");
            return false;
        }
        return true;
    }

    public function getSignatureKey()
    {
        return $this->signatureKey;
    }

    public function getEnv()
    {
        return $this->env;
    }
}