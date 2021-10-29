<?php

namespace Revosystems\Redsys\Http\Livewire;

use Livewire\Component;
use Revosystems\Redsys\Models\ChargeRequest;
use Revosystems\Redsys\Models\ChargeResult;
use Revosystems\Redsys\Models\PaymentHandler;
use Revosystems\Redsys\Models\RedsysPaymentGateway;

class RedsysForm extends Component
{
    protected $listeners = [
        'onCardFormSubmit',
        'onTokenizedCardPressed',
        'onPaymentCompleted',
    ];

    public $shouldSaveCard = false;
    public $orderReference;
    public $customerToken;
    public $iframeUrl;
    public $amount;
    public $hasCards;
    public $redsysFormId;

    public function mount(string $redsysFormId, string $orderReference, string $amount, string $customerToken, bool $hasCards)
    {
        $this->amount           = $amount;
        $this->redsysFormId     = $redsysFormId;
        $this->orderReference   = $orderReference;
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
        PaymentHandler::get($this->orderReference)->onPaymentCompleted();
    }
    
    public function onCardFormSubmit($operationId, $extraInfo) : void
    {
        $chargeRequest = ChargeRequest::makeWithOperationId($this->orderReference, $operationId, $extraInfo);
        if ($this->shouldSaveCard) {
            $chargeRequest->customerToken = $this->customerToken;
        }
        $this->emit('payResponse', $this->chargeToRedsys($chargeRequest)->gatewayResponse);
    }

    public function onTokenizedCardPressed($cardId, $extraInfo) : void
    {
        $chargeRequest = ChargeRequest::makeWithCard($this->orderReference, $cardId, $extraInfo);
        $this->emit('payResponse', $this->chargeToRedsys($chargeRequest)->gatewayResponse);
    }

    protected function chargeToRedsys(ChargeRequest $chargeRequest) : ChargeResult
    {
        $order = PaymentHandler::get($this->orderReference)->order;
        return RedsysPaymentGateway::get()->charge($chargeRequest, $order);
    }
}
