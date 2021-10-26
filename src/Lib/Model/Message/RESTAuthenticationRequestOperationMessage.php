<?php

namespace Revosystems\RedsysPayment\Lib\Model\Message;

use Revosystems\RedsysPayment\Lib\Constants\RESTConstants;
use Revosystems\RedsysPayment\Lib\Model\RESTRequestInterface;

/**
 * @XML_ELEM=DATOSENTRADA
 */
class RESTAuthenticationRequestOperationMessage extends RESTRequestOperationMessage implements RESTRequestInterface
{
    /**
     * @XML_ELEM=DS_MERCHANT_MERCHANTCODE
     */
    protected $merchant;

    /**
     * @XML_ELEM=DS_MERCHANT_TERMINAL
     */
    protected $terminal;

    /**
     * @XML_ELEM=DS_MERCHANT_ORDER
     */
    protected $order;

    /**
     * @XML_ELEM=DS_MERCHANT_IDOPER
     */
    protected $operID;

    /**
     * @XML_ELEM=DS_MERCHANT_TRANSACTIONTYPE
     */
    protected $transactionType;

    /**
     * @XML_ELEM=DS_MERCHANT_CURRENCY
     */
    protected $currency;

    /**
     * @XML_ELEM=DS_MERCHANT_AMOUNT
     */
    protected $amount;

    /**
     * Card Number
     */
    protected $cardNumber;

    /**
     * Card ExpiryDate
     */
    protected $cardExpiryDate;

    /**
     * Card CVV2
     */
    protected $cvv2;

    /**
     * Other operation parameter
     */
    protected $parameters = [];

    /**
     * 3DSecure information
     * @XML_ELEM=DS_MERCHANT_EMV3DS
     */
    protected $emv = [];

    /**
     * Method for set the EMV3DS return parameters for a V1 challenge Request
     * $pares protocolVersion 1.0.2 authentication parameter
     * $md protocolVersion 1.0.2 authentication parameter
     */
    public function challengeRequestV1($pares, $md)
    {
        $this->addEmvParameter(RESTConstants::$REQUEST_MERCHANT_EMV3DS_THREEDSINFO, RESTConstants::$REQUEST_MERCHANT_EMV3DS_CHALLENGEREQUESTRESPONSE);
        $this->addEmvParameter(RESTConstants::$REQUEST_MERCHANT_EMV3DS_PROTOCOLVERSION, RESTConstants::$REQUEST_MERCHANT_EMV3DS_PROTOCOLVERSION_102);
        $this->addEmvParameter(RESTConstants::$REQUEST_MERCHANT_EMV3DS_PARES, $pares);
        $this->addEmvParameter(RESTConstants::$REQUEST_MERCHANT_EMV3DS_MD, $md);
    }

    /**
     * Method for set the EMV3DS return parameters for a V2 challenge Request
     * $protocolVersion protocolVersion 2.X.0 authentication parameter
     * $cres protocolVersion 2.X.0 authentication parameter
     */
    public function challengeRequestV2($protocolVersion, $cres)
    {
        $this->addEmvParameter(RESTConstants::$REQUEST_MERCHANT_EMV3DS_THREEDSINFO, RESTConstants::$REQUEST_MERCHANT_EMV3DS_CHALLENGEREQUESTRESPONSE);
        $this->addEmvParameter(RESTConstants::$REQUEST_MERCHANT_EMV3DS_PROTOCOLVERSION, $protocolVersion);
        $this->addEmvParameter(RESTConstants::$REQUEST_MERCHANT_EMV3DS_CRES, $cres);
    }
}
