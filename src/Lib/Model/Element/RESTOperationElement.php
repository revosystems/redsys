<?php

namespace Revosystems\RedsysPayment\Lib\Model\Element;

use Revosystems\RedsysPayment\Lib\Constants\RESTConstants;
use Revosystems\RedsysPayment\Lib\Model\RESTGenericXml;

/**
 * @XML_ELEM=OPERACION
 */
class RESTOperationElement extends RESTGenericXml
{
    /**
     * @XML_ELEM=Ds_Amount
     */
    private $amount;

    /**
     * @XML_ELEM=Ds_Currency
     */
    private $currency;

    /**
     * @XML_ELEM=Ds_Order
     */
    private $order;

    /**
     * @XML_ELEM=Ds_Signature
     */
    private $signature;

    /**
     * @XML_ELEM=Ds_MerchantCode
     */
    private $merchant;

    /**
     * @XML_ELEM=Ds_Terminal
     */
    private $terminal;

    /**
     * @XML_ELEM=Ds_Response
     */
    private $responseCode;

    /**
     * @XML_ELEM=Ds_AuthorisationCode
     */
    private $authCode;

    /**
     * @XML_ELEM=Ds_TransactionType
     */
    private $transactionType;

    /**
     * @XML_ELEM=Ds_SecurePayment
     */
    private $securePayment;

    /**
     * @XML_ELEM=Ds_Language
     */
    private $language;

    /**
     * @XML_ELEM=Ds_MerchantData
     */
    private $merchantData;

    /**
     * @XML_ELEM=Ds_Card_Country
     */
    private $cardCountry;

    /**
     * @XML_ELEM=Ds_CardNumber
     */
    private $cardNumber;

    /**
     * @XML_ELEM=Ds_Card_Brand
     */
    private $cardBrand;


    /**
     * @XML_ELEM=Ds_Card_Type
     */
    private $cardType;

    /**
     * @XML_ELEM=Ds_ExpiryDate
     */
    private $expiryDate;

    /**
     * @XML_ELEM=Ds_Merchant_Identifier
     */
    private $merchantIdentifier;

    /**
     * @XML_ELEM=Ds_Card_PSD2
     */
    private $psd2;

    /**
     * @XML_ELEM=Ds_Excep_SCA
     */
    private $exemption;

    /**
     * @XML_ELEM=Ds_Merchant_Cof_Txnid
     */
    private $cofTxnid;

    /**
     * @XML_ELEM=Ds_EMV3DS
     */
    private $emv;

    /*
     * @XML ELEM=InfoMonedaTarjeta
     */
    private $infoMonedaTarjeta;
    private $protocolVersion;
    private $threeDSInfo;

    /**
     * GETTERS & SETTERS OF VARS
     */
    public function getAmount()
    {
        return $this->amount;
    }

    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    public function setCurrency($currency)
    {
        $this->currency = $currency;
        return $this;
    }

    public function getOrder()
    {
        return $this->order;
    }

    public function setOrder($order)
    {
        $this->order = $order;
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

    public function getMerchant()
    {
        return $this->merchant;
    }

    public function setMerchant($merchant)
    {
        $this->merchant = $merchant;
        return $this;
    }

    public function getTerminal()
    {
        return $this->terminal;
    }

    public function setTerminal($terminal)
    {
        $this->terminal = $terminal;
        return $this;
    }

    public function getResponseCode()
    {
        return $this->responseCode;
    }

    public function setResponseCode($responseCode)
    {
        $this->responseCode = $responseCode;
        return $this;
    }

    public function getAuthCode()
    {
        return $this->authCode;
    }

    public function setAuthCode($authCode)
    {
        $this->authCode = $authCode;
        return $this;
    }

    public function getTransactionType()
    {
        return $this->transactionType;
    }

    public function setTransactionType($transactionType)
    {
        $this->transactionType = $transactionType;
        return $this;
    }

    public function getSecurePayment()
    {
        return $this->securePayment;
    }

    public function setSecurePayment($securePayment)
    {
        $this->securePayment = $securePayment;
        return $this;
    }

    public function getLanguage()
    {
        return $this->language;
    }

    public function setLanguage($language)
    {
        $this->language = $language;
        return $this;
    }

    public function getMerchantData()
    {
        return $this->merchantData;
    }

    public function setMerchantData($merchantData)
    {
        $this->merchantData = $merchantData;
        return $this;
    }

    public function getCardCountry()
    {
        return $this->cardCountry;
    }

    public function setCardCountry($cardCountry)
    {
        $this->cardCountry = $cardCountry;
        return $this;
    }

    public function getCardNumber()
    {
        return $this->cardNumber;
    }

    public function setCardNumber($cardNumber)
    {
        $this->cardNumber = $cardNumber;
        return $this;
    }

    public function getCardBrand()
    {
        return $this->cardBrand;
    }

    public function setCardBrand($cardBrand)
    {
        $this->cardBrand = $cardBrand;
        return $this;
    }

    public function getCardType()
    {
        return $this->cardType;
    }

    public function setCardType($cardType)
    {
        $this->cardType = $cardType;
        return $this;
    }

    public function getExpiryDate()
    {
        return $this->expiryDate;
    }

    public function setExpiryDate($expiryDate)
    {
        $this->expiryDate = $expiryDate;
        return $this;
    }

    public function getMerchantIdentifier()
    {
        return $this->merchantIdentifier;
    }

    public function setMerchantIdentifier($merchantIdentifier)
    {
        $this->merchantIdentifier = $merchantIdentifier;
        return $this;
    }

    public function getPsd2()
    {
        return $this->psd2;
    }

    public function setPsd2($psd2)
    {
        $this->psd2 = $psd2;
        return $this;
    }

    public function getExemption()
    {
        return $this->exemption;
    }

    public function setExemption($exemption)
    {
        $this->exemption = $exemption;
        return $this;
    }

    public function getCof()
    {
        return $this->cof;
    }

    public function setCof($cof)
    {
        $this->cof = $cof;
        return $this;
    }

    public function getEmv()
    {
        if ($this->emv == null) {
            return null;
        }

        return json_encode($this->emv);
    }

    public function setEmv($emv)
    {
        $this->emv = $emv;
        return $this;
    }

    public function getCofTxnid()
    {
        return $this->cofTxnid;
    }

    public function setCofTxnid($cofTxnid)
    {
        $this->cofTxnid = $cofTxnid;
        return $this;
    }

    public function getInfoMonedaTarjeta()
    {
        return $this->infoMonedaTarjeta;
    }

    public function setInfoMonedaTarjeta($infoMonedaTarjeta)
    {
        $this->infoMonedaTarjeta = $infoMonedaTarjeta;
        return $this;
    }

    public function getThreeDSInfo()
    {
        return $this->emv[RESTConstants::$RESPONSE_JSON_THREEDSINFO_ENTRY] ?? null;
    }

    public function setThreeDSInfo($threeDSInfo)
    {
        $this->threeDSInfo = $threeDSInfo;
        return $this;
    }

    public function getProtocolVersion()
    {
        return $this->emv[RESTConstants::$RESPONSE_JSON_PROTOCOL_VERSION_ENTRY] ?? null;
    }

    public function setProtocolVersion($protocolVersion)
    {
        $this->protocolVersion = $protocolVersion;
        return $this;
    }

    public function getAcsUrl()
    {
        return $this->emv[RESTConstants::$RESPONSE_JSON_ACS_ENTRY] ?? null;
    }

    public function getPaRequest()
    {
        return $this->emv[RESTConstants::$RESPONSE_JSON_PAREQ_ENTRY] ?? null;
    }

    public function getAutSession()
    {
        return $this->emv[RESTConstants::$RESPONSE_JSON_MD_ENTRY] ?? null;
    }

    public function requiresSCA()
    {
        return strlen($this->getAcsUrl()) > 10;
    }
}
