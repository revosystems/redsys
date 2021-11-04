<?php

namespace Revosystems\Redsys\Lib\Model\Message;

use Revosystems\Redsys\Lib\Constants\RESTConstants;

/**
 * @XML_ELEM=REQUEST
 */
class RESTRefundRequestOperationMessage  extends RESTInitialRequestOperationMessage
{
    protected function transactionType() : string
    {
        return RESTConstants::$REFUND;
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
}
