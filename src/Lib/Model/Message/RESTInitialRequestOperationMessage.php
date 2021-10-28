<?php

namespace Revosystems\Redsys\Lib\Model\Message;

use Revosystems\Redsys\Lib\Constants\RESTConstants;
use Illuminate\Support\Facades\Log;

/**
 * @XML_ELEM=REQUEST
 */
class RESTInitialRequestOperationMessage extends RESTRequestOperationMessage
{
    public function __construct()
    {
        $this->signatureVersion = RESTConstants::$REQUEST_SIGNATUREVERSION_VALUE;
    }

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
    protected $cardNumber = null;

    /**
     * Expiry Date
     * @XML_ELEM=DS_MERCHANT_EXPIRYDATE
     */
    protected $cardExpiryDate = null;

    /**
     * Expiry Date
     * @XML_ELEM=DS_MERCHANT_CVV2
     */
    protected $cvv2 = null;

    /**
     * @XML_ELEM=Ds_MerchantParameters
     */
    protected $datosEntradaB64 = null;

    /**
     * @XML_CLASS=RESTOperationMessage
     */
    protected $datosEntrada = null;

    /**
     * @XML_ELEM=DS_SIGNATUREVERSION
     */
    protected $signatureVersion = null;

    /**
     * @XML_ELEM=DS_SIGNATURE
     */
    protected $signature = null;

    public function demandCardData()
    {
        $this->addEmvParameter(RESTConstants::$REQUEST_MERCHANT_EMV3DS_THREEDSINFO, RESTConstants::$REQUEST_MERCHANT_EMV3DS_CARDDATA);
        return $this;
    }

    public function getDatosEntrada()
    {
        return $this->datosEntrada;
    }

    public function setDatosEntrada($datosEntrada)
    {
        $this->datosEntrada    = $datosEntrada;
        Log::debug("[REDSYS] Request JSON: {$this->datosEntrada->toJson()}");
        $this->datosEntradaB64 = base64_encode($this->datosEntrada->toJson());
        return $this;
    }

    public function getDatosEntradaB64()
    {
        return $this->datosEntradaB64;
    }

    public function setDatosEntradaB64($datosEntradaB64)
    {
        $this->datosEntradaB64 = $datosEntradaB64;
        return $this;
    }

    public function getSignatureVersion()
    {
        return $this->signatureVersion;
    }

    public function sreVersion($signatureVersion)
    {
        $this->signatureVersion = $signatureVersion;
        return $this;
    }

    public function getSignature()
    {
        return $this->signature;
    }

    public function setSignature($signature)
    {
        $this->signature = $signature;
        return $this;
    }
}
