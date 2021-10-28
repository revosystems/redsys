<?php

namespace Revosystems\Redsys\Lib\Model;

interface RESTResponseInterface
{
    public function setResult($code);
    public function getResult();
    public function getTransactionType();
    public function PSD2analysis();
    public function protocolVersionAnalysis();
    public function getMDParameter();
    public function getAcsURLParameter();
    public function getPAReqParameter();
    public function getThreeDSServerTransID();
    public function getThreeDSMethodURL();
    public function getThreeDSInfo();
    public function getCreqParameter();
    public function getExemption();
    public function getCOFTxnid();
}
