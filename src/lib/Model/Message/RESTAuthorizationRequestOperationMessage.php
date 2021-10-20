<?php

namespace App\Services\Redsys\Lib\Model\Message;

use App\Services\Redsys\Lib\Constants\RESTConstants;
use App\Services\Redsys\Lib\Model\RESTRequestInterface;

/**
 * @XML_ELEM=DATOSENTRADA
 */
class RESTAuthorizationRequestOperationMessage extends RESTRequestOperationMessage implements RESTRequestInterface
{
    /**
     * Merchant code (FUC)
     * @XML_ELEM=DS_MERCHANT_MERCHANTCODE
     */
    protected $merchant = null;

    /**
     * Terminal code
     * @XML_ELEM=DS_MERCHANT_TERMINAL
     */
    protected $terminal = null;

    /**
     * Operation order code
     * @XML_ELEM=DS_MERCHANT_ORDER
     */
    protected $order = null;

    /**
     * Operation ID code
     * @XML_ELEM=DS_MERCHANT_IDOPER
     */
    protected $operID = null;

    /**
     * Operation type
     * @XML_ELEM=DS_MERCHANT_TRANSACTIONTYPE
     */
    protected $transactionType = null;

    /**
     * Currency code (ISO 4217)
     * @XML_ELEM=DS_MERCHANT_CURRENCY
     */
    protected $currency = null;

    /**
     * Operation amount, without decimal separation
     * @XML_ELEM=DS_MERCHANT_AMOUNT
     */
    protected $amount = null;

    /**
     * Other operation parameters
     */
    protected $parameters = [];

    /**
     * 3DSecure information
     * @XML_ELEM=DS_MERCHANT_EMV3DS
     */
    protected $emv = null;

    /**
     * Card Number
     * @XML_ELEM=DS_MERCHANT_PAN
     */
    protected $cardNumber;

    /**
     * Expiry Date
     * @XML_ELEM=DS_MERCHANT_EXPIRYDATE
     */
    protected $cardExpiryDate;

    /**
     * Expiry Date
     * @XML_ELEM=DS_MERCHANT_CVV2
     */
    protected $cvv2;

    /**
     * Method for set the EMV3DS protocolVersionV1 parameters
     */
    public function setEMV3DSParamsV1()
    {
        $this->addEmvParameter(RESTConstants::$REQUEST_MERCHANT_EMV3DS_THREEDSINFO, RESTConstants::$REQUEST_MERCHANT_EMV3DS_AUTHENTICACIONDATA);
        $this->addEmvParameter(RESTConstants::$REQUEST_MERCHANT_EMV3DS_PROTOCOLVERSION, RESTConstants::$REQUEST_MERCHANT_EMV3DS_PROTOCOLVERSION_102);
        $this->addEmvParameter(RESTConstants::$REQUEST_MERCHANT_EMV3DS_BROWSER_ACCEPT_HEADER, RESTConstants::$REQUEST_MERCHANT_EMV3DS_BROWSER_ACCEPT_HEADER_VALUE);
        $this->addEmvParameter(RESTConstants::$REQUEST_MERCHANT_EMV3DS_BROWSER_USER_AGENT, RESTConstants::$REQUEST_MERCHANT_EMV3DS_BROWSER_USER_AGENT_VALUE);
    }

    public function setEMV3DSParamsV2(
        $protocolVersion,
        $browserAcceptHeader,
        $browserUserAgent,
        $browserJavaEnable,
        $browserJavascriptEnable,
        $browserIP,
        $browserLanguage,
        $browserColorDepth,
        $browserScreenHeight,
        $browserScreenWidth,
        $browserTZ,
        $threeDSServerTransID,
        $notificationURL,
        $threeDSCompInd
    ) {
        $this->addEmvParameter(RESTConstants::$REQUEST_MERCHANT_EMV3DS_THREEDSINFO, RESTConstants::$REQUEST_MERCHANT_EMV3DS_AUTHENTICACIONDATA);
        $this->addEmvParameter(RESTConstants::$REQUEST_MERCHANT_EMV3DS_PROTOCOLVERSION, $protocolVersion);
        $this->addEmvParameter(RESTConstants::$REQUEST_MERCHANT_EMV3DS_BROWSER_ACCEPT_HEADER, $browserAcceptHeader);
        $this->addEmvParameter(RESTConstants::$REQUEST_MERCHANT_EMV3DS_BROWSER_USER_AGENT, $browserUserAgent);
        $this->addEmvParameter(RESTConstants::$REQUEST_MERCHANT_EMV3DS_BROWSER_JAVA_ENABLE, $browserJavaEnable);
        $this->addEmvParameter(RESTConstants::$REQUEST_MERCHANT_EMV3DS_BROWSER_JAVASCRIPT_ENABLE, $browserJavascriptEnable);
        $this->addEmvParameter(RESTConstants::$REQUEST_MERCHANT_EMV3DS_BROWSER_IP, $browserIP);
        $this->addEmvParameter(RESTConstants::$REQUEST_MERCHANT_EMV3DS_BROWSER_LANGUAGE, $browserLanguage);
        $this->addEmvParameter(RESTConstants::$REQUEST_MERCHANT_EMV3DS_BROWSER_COLORDEPTH, $browserColorDepth);
        $this->addEmvParameter(RESTConstants::$REQUEST_MERCHANT_EMV3DS_BROWSER_SCREEN_HEIGHT, $browserScreenHeight);
        $this->addEmvParameter(RESTConstants::$REQUEST_MERCHANT_EMV3DS_BROWSER_SCREEN_WIDTH, $browserScreenWidth);
        $this->addEmvParameter(RESTConstants::$REQUEST_MERCHANT_EMV3DS_BROWSER_TZ, $browserTZ);
        $this->addEmvParameter(RESTConstants::$REQUEST_MERCHANT_EMV3DS_THREEDSSERVERTRANSID, $threeDSServerTransID);
        $this->addEmvParameter(RESTConstants::$REQUEST_MERCHANT_EMV3DS_NOTIFICATIONURL, $notificationURL);
        $this->addEmvParameter(RESTConstants::$REQUEST_MERCHANT_EMV3DS_THREEDSCOMPIND, $threeDSCompInd);
    }
}
