<?php


namespace Revosystems\Redsys\Services;

use Revosystems\Redsys\Lib\Utils\Price;

class RedsysPayment
{
    public $externalReference;
    public $price;
    public $tenant;

    public $cres;
    public $paRes;
    public $mD;
    public $protocolVersion;


    public function __construct(string $externalReference, string $tenant, int $amount, string $currency)
    {
        $this->externalReference    = $externalReference;
        $this->tenant               = $tenant;
        $this->price                = new Price($amount, $currency);
    }

    public function setCres(?string $cres) : self
    {
        $this->cres = $cres;
        return $this;
    }

    public function setPaResAndMD(?string $paRes, ?string $mD) : self
    {
        $this->paRes    = $paRes;
        $this->mD       = $mD;
        return $this;
    }

    public function setProtocolVersion(string $protocolVersion) : self
    {
        $this->protocolVersion = $protocolVersion;
        return $this;
    }
}
