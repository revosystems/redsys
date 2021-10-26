<?php

namespace Revosystems\RedsysPayment\Lib\Model\Message;

use Revosystems\RedsysPayment\Lib\Constants\RESTConstants;
use Revosystems\RedsysPayment\Lib\Model\Element\RESTOperationElement;
use Revosystems\RedsysPayment\Lib\Model\RESTGenericXml;
use Revosystems\RedsysPayment\Lib\Model\RESTResponseInterface;

/**
 * @XML_ELEM=RETORNOXML
 */
class RESTResponseMessage extends RESTGenericXml implements RESTResponseInterface
{
    private $result;
    private $apiCode;

    /**
     * @XML_CLASS=RESTOperationElement
     */
    private $operation;

    public function getResult()
    {
        return $this->result;
    }

    public function setResult($result)
    {
        $this->result = $result;
        return $this;
    }

    public function getApiCode()
    {
        return $this->apiCode;
    }

    public function setApiCode($apiCode)
    {
        $this->apiCode = $apiCode;
    }

    public function getOperation() : RESTOperationElement
    {
        return $this->operation;
    }

    public function setOperation($operation)
    {
        $this->operation = $operation;
        return $this;
    }

    public function getTransactionType()
    {
        if ($this->getOperation() === null) {
            return null;
        }
        return $this->getOperation()->getTransactionType();
    }

    /**
     * Method to get the protocolVersion
     * return $protocolVersion: 3EMV3DS authentications version
     */
    public function protocolVersionAnalysis()
    {
        $version3DSecure = $this->getEmvVarWithConstant(RESTConstants::$RESPONSE_MERCHANT_EMV3DS_PROTOCOLVERSION);
        if (strcmp(RESTConstants::$RESPONSE_MERCHANT_EMV3DS_PROTOCOLVERSION_102, $version3DSecure) == 0) {
            return "1.0.2";
        }
        return $version3DSecure;
    }

    /**
     * Method to get the PSD2 result value (inform us if authentication is mandatory)
     * return $psd2: inform if authenticacion its mandatory (Y/N)
     */
    public function PSD2analysis()
    {
        return $this->getOperation()->getPsd2();
    }

    /**
     * Method to get the Exemption result value
     * return $exemption: exemption allowed to the commerce
     */
    public function getExemption()
    {
        return $this->getOperation()->getExemption();
    }

    /**
     * Method to get the MD parameter value (protocolVersion 1.0.2)
     * return $md: protocolVersion 1.0.2 authentication parameter
     */
    public function getMDParameter()
    {
        return $this->getEmvVarWithConstant(RESTConstants::$RESPONSE_MERCHANT_EMV3DS_MD);
    }

    /**
     * Method to get the ACSURL parameter value (protocolVersion 1.0.2)
     * return $acsURL: protocolVersion 1.0.2 authentication URL
     */
    public function getAcsURLParameter()
    {
        return $this->getEmvVarWithConstant(RESTConstants::$RESPONSE_MERCHANT_EMV3DS_ACSURL);
    }

    /**
     * Method to get the PAREQ parameter value (protocolVersion 1.0.2)
     * return protocolVersion 1.0.2 authentication parameter
     */
    public function getPAReqParameter()
    {
        return $this->getEmvVarWithConstant(RESTConstants::$RESPONSE_MERCHANT_EMV3DS_PAREQ);
    }

    /**
     * Method to get the threeDSServerTransID parameter value (protocolVersion 2.X.0)
     * return protocolVersion 2.X.0 authentication parameter
     */
    public function getThreeDSServerTransID()
    {
        return $this->getEmvVarWithConstant(RESTConstants::$RESPONSE_MERCHANT_EMV3DS_THREEDSSERVERTRANSID);
    }

    /**
     * Method to get the threeDSInfo parameter value
     * return Authentication parameter Info for each operation
     */
    public function getThreeDSInfo()
    {
        return $this->getEmvVarWithConstant(RESTConstants::$RESPONSE_MERCHANT_EMV3DS_THREEDSINFO);
    }

    /**
     * Method to get the threeDSMethodURL parameter value (protocolVersion 2.X.0)
     * return protocolVersion 2.X.0 authentication URL
     */
    public function getThreeDSMethodURL()
    {
        return $this->getEmvVarWithConstant(RESTConstants::$RESPONSE_MERCHANT_EMV3DS_THREEDSMETHODURL);
    }

    /**
     * Method to get the CREQ parameter value (protocolVersion 2.X.0)
     * return protocolVersion 2.X.0 authentication parameter
     */
    public function getCreqParameter()
    {
        return $this->getEmvVarWithConstant(RESTConstants::$RESPONSE_MERCHANT_EMV3DS_CREQ);
    }

    /**
     * Method to get Ds_Merchant_Cof_Txnid
     */
    public function getCOFTxnid()
    {
        return $this->getOperation()->getCofTxnid();
    }

    private function getEmv()
    {
        return $this->getOperation()->getEmv() ? json_decode($this->getOperation()->getEmv(), true) : null;
    }

    private function getEmvVarWithConstant(string $constant)
    {
        return $this->getEmv()[$constant] ?? "";
    }
}
