<?php


namespace Revosystems\Redsys\Lib\Model\Message;

use Revosystems\Redsys\Lib\Constants\RESTConstants;
use Revosystems\Redsys\Lib\Model\RESTGenericXml;
use Revosystems\Redsys\Models\RedsysConfig;
use Revosystems\Redsys\Services\RedsysChargeRequest;
use Revosystems\Redsys\Services\RedsysPayment;

abstract class RESTRequestOperationMessage extends RESTGenericXml
{
    public function generate(RedsysConfig $config, RedsysPayment $chargePayment, RedsysChargeRequest $chargeRequest) : RESTRequestOperationMessage
    {
        $this->setMerchant($config->code)
            ->setTerminal($config->terminal)
            ->setOrder($chargeRequest->paymentReference)
            ->setTransactionType(RESTConstants::$AUTHORIZATION);
        if ($chargePayment->price) {
            $this->setAmount($chargePayment->price->amount)
                ->setCurrency($chargePayment->price->currency->numericCode()); // ISO-4217 numeric currency code
        }

        if ($chargePayment->externalReference) {
            $this->addParameter("DS_MERCHANT_REVO_ORDER_ID", $chargePayment->externalReference);
        }
        $this->addParameter("DS_MERCHANT_REVO_TENANT", $chargePayment->tenant);
        return $this;
    }

    public function setCard(RedsysChargeRequest $chargePaymentRequest) : self
    {
        if ($chargePaymentRequest->cardId) {
            $this->useReference($chargePaymentRequest->cardId);
        } else {
            $this->setOperID($chargePaymentRequest->operationId);
        }
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

    public function getOrder()
    {
        return $this->order;
    }

    public function setOrder($order)
    {
        $this->order = $order;
        return $this;
    }

    public function getOperID()
    {
        return $this->operID;
    }

    public function setOperID($operID)
    {
        $this->operID = $operID;
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

    public function getCurrency()
    {
        return $this->currency;
    }

    public function setCurrency(string $currency)
    {
        $this->currency = $currency;
        return $this;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function setAmount(int $amount)
    {
        $this->amount = $amount;
        return $this;
    }

    public function getCardNumber()
    {
        return $this->cardNumber;
    }

    public function setCardNumber($cardNumber)
    {
        $this->cardNumber = $cardNumber;
    }

    public function getCardExpiryDate()
    {
        return $this->cardExpiryDate;
    }

    public function setCardExpiryDate($cardExpiryDate)
    {
        $this->cardExpiryDate = $cardExpiryDate;
    }

    public function getCvv2()
    {
        return $this->cvv2;
    }

    public function setCvv2($cvv2)
    {
        $this->cvv2 = $cvv2;
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    public function addParameter($key, $value)
    {
        $this->parameters [$key] = $value;
    }

    public function getEmv()
    {
        return ! $this->emv ? null : json_encode($this->emv);
    }

    public function setEmv($emv)
    {
        $this->emv = $emv;
        return $this;
    }

    /**
     * Flag for reference creation (card token for merchant to use in other operations)
     */
    public function createReference()
    {
        $this->addParameter(RESTConstants::$REQUEST_MERCHANT_IDENTIFIER, RESTConstants::$REQUEST_MERCHANT_IDENTIFIER_REQUIRED);
    }

    /**
     * Method for using a reference created before for the operation
     * $reference: the reference string to be used
     */
    public function useReference($reference)
    {
        $this->addParameter(RESTConstants::$REQUEST_MERCHANT_IDENTIFIER, $reference);
    }

    /**
     * Flag for direct payment operation.
     * Direct payment operation implies:
     * 1) No-secure operation
     * 2) No-DCC operative appliance
     */
    public function useDirectPayment()
    {
        $this->addParameter(RESTConstants::$REQUEST_MERCHANT_DIRECTPAYMENT, RESTConstants::$REQUEST_MERCHANT_DIRECTPAYMENT_TRUE);
    }

    /**
     * For use a MOTO Payment
     */
    public function useMOTOPayment()
    {
        $this->addParameter(RESTConstants::$REQUEST_MERCHANT_DIRECTPAYMENT, RESTConstants::$REQUEST_MERCHANT_DIRECTPAYMENT_MOTO);
    }

    public function addEmvParameters($parameters)
    {
        if (! $this->emv) {
            $this->emv = [];
        }

        foreach ($parameters as $key => $value) {
            $this->emv[$key]=$value;
        }
    }

    public function addEmvParameter($name, $value)
    {
        if (! $this->emv) {
            $this->emv = [];
        }

        $this->emv[$name] = $value;
    }

    /**
     * Method for the first COF operation
     */
    public function setCOFOperation($cofType)
    {
        $this->addParameter(RESTConstants::$REQUEST_MERCHANT_COF_INI, RESTConstants::$REQUEST_MERCHANT_COF_INI_TRUE);
        $this->addParameter(RESTConstants::$REQUEST_MERCHANT_COF_TYPE, $cofType);
    }

    /**
     * Method for a COF operation
     */
    public function setCOFTxnid($txnid)
    {
        $this->addParameter(RESTConstants::$REQUEST_MERCHANT_COF_TXNID, $txnid);
    }

    /**
     * Flag for secure operation.
     * If is used, after the response, the process will be stopped due to the authentication process
     */
    // 		public function useSecurePayment() {
    // 			$this->addParameter ( RESTConstants::$REQUEST_MERCHANT_DIRECTPAYMENT, RESTConstants::$REQUEST_MERCHANT_DIRECTPAYMENT_3DS );
    // 		}

    /**
     * Method for set the authentication exemption for V2 EMV3DS
     * $exemption: constant of the exemption the commerce want to use
     */
    public function setExemption($exemption)
    {
        if (strcmp("LWV", $exemption) == 0) {
            $this->addParameter(RESTConstants::$REQUEST_MERCHANT_EXEMPTION, RESTConstants::$REQUEST_MERCHANT_EXEMPTION_VALUE_LWV);
        } elseif (strcmp("TRA", $exemption) == 0) {
            $this->addParameter(RESTConstants::$REQUEST_MERCHANT_EXEMPTION, RESTConstants::$REQUEST_MERCHANT_EXEMPTION_VALUE_TRA);
        } elseif (strcmp("MIT", $exemption) == 0) {
            $this->addParameter(RESTConstants::$REQUEST_MERCHANT_EXEMPTION, RESTConstants::$REQUEST_MERCHANT_EXEMPTION_VALUE_MIT);
        } elseif (strcmp("COR", $exemption) == 0) {
            $this->addParameter(RESTConstants::$REQUEST_MERCHANT_EXEMPTION, RESTConstants::$REQUEST_MERCHANT_EXEMPTION_VALUE_COR);
        } elseif (strcmp("ATD", $exemption) == 0) {
            $this->addParameter(RESTConstants::$REQUEST_MERCHANT_EXEMPTION, RESTConstants::$REQUEST_MERCHANT_EXEMPTION_VALUE_ATD);
        } elseif (strcmp("NDF", $exemption) == 0) {
            $this->addParameter(RESTConstants::$REQUEST_MERCHANT_EXEMPTION, RESTConstants::$REQUEST_MERCHANT_EXEMPTION_VALUE_NDF);
        }
    }

}