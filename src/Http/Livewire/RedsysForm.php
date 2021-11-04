<?php

namespace Revosystems\Redsys\Http\Livewire;

use Livewire\Component;
use Revosystems\Redsys\Services\RedsysChargePayment;
use Revosystems\Redsys\Services\RedsysChargeRequest;
use Revosystems\Redsys\Models\RedsysPaymentGateway;

class RedsysForm extends Component
{
    protected $listeners = [
        'onCardFormSubmit',
        'onTokenizedCardPressed',
        'onPaymentCompleted',
    ];

    public $shouldSaveCard = false;
    public $paymentReference;
    public $customerToken;
    public $iframeUrl;
    public $price;
    public $hasCards;
    public $redsysFormId;

    public function mount(string $redsysFormId, string $paymentReference, string $price, string $customerToken, bool $hasCards)
    {
        $this->price            = $price;
        $this->redsysFormId     = $redsysFormId;
        $this->paymentReference = $paymentReference;
        $this->customerToken    = $customerToken;
        $this->hasCards         = $hasCards;
        $this->iframeUrl        = RedsysPaymentGateway::isTestEnvironment() ? 'https://sis-t.redsys.es:25443/sis/NC/sandbox/redsysV2.js' : 'https://sis.redsys.es/sis/NC/redsysV2.js';
    }

    public function render()
    {
        return view('redsys::livewire.redsys-form');
    }

    public function onPaymentCompleted() : void
    {
        RedsysChargePayment::get($this->paymentReference)->payHandler->onPaymentSucceed($this->paymentReference);
    }
    
    public function onCardFormSubmit(string $operationId, array $extraInfo) : void
    {
        $chargeRequest = RedsysChargeRequest::makeWithOperationId($this->paymentReference, $operationId, $extraInfo);
        if ($this->shouldSaveCard) {
            $chargeRequest->customerToken = $this->customerToken;
        }
        $this->emit('payResponse', RedsysPaymentGateway::get()->charge(
            RedsysChargePayment::get($this->paymentReference),
            $chargeRequest
        )->gatewayResponse);
    }

    public function onTokenizedCardPressed(string $cardId, array $extraInfo) : void
    {
        $chargeRequest = RedsysChargeRequest::makeWithCard($this->paymentReference, $cardId, $extraInfo);
        $this->emit('payResponse', RedsysPaymentGateway::get()->charge(
            RedsysChargePayment::get($this->paymentReference),
            $chargeRequest
        )->gatewayResponse);
    }
}
