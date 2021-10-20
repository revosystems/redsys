<?php

namespace Revosystems\RedsysGateway\Lib\Model\Message;

use Revosystems\RedsysGateway\Lib\Constants\RESTConstants;
use Revosystems\RedsysGateway\Models\ChargeRequest;

/**
 * @XML_ELEM=REQUEST
 */
class RESTRefundRequestOperationMessage extends RESTInitialRequestOperationMessage
{
    public function generate(ChargeRequest $data, $posOrderId, $amount, $currency)
    {
        $this->setAmount($amount); // i.e. 1,23 (decimal point depends on currency code)
        $this->setCurrency($currency); // ISO-4217 numeric currency code
        $this->setOrder($data->orderId);
        $this->setTransactionType(RESTConstants::$REFUND);
        $this->addParameter("DS_MERCHANT_REVO_ORDER_ID", $posOrderId);
        return $this;
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
