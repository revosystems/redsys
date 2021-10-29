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
    ];

    public $shouldSaveCard = false;
    public $orderReference;
    public $customerToken;
    public $cards;
    public $iframeUrl;
    public $amount;
    public $hasCards;
    public $redsysFormId;

    public function mount($iframeUrl, $redsysFormId, $amount, $orderReference, $customerToken, $hasCards)
    {
        $this->amount           = $amount;
        $this->iframeUrl        = $iframeUrl;
        $this->redsysFormId     = $redsysFormId;
        $this->orderReference   = $orderReference;
        $this->customerToken    = $customerToken;
        $this->hasCards         = $hasCards;
    }

    public function render()
    {
        return view('redsys::livewire.redsys-form');
    }

    public function onCardFormSubmit($operationId, $params)
    {
        $chargeRequest = ChargeRequest::makeWithOperationId($this->orderReference, $operationId, $this->shouldSaveCard, $this->customerToken, $params);
        $this->emit('payResponse', $this->chargeToRedsys($chargeRequest)->gatewayResponse);
    }

    protected function chargeToRedsys(ChargeRequest $chargeRequest) : ChargeResult
    {
        $order = PaymentHandler::get($this->orderReference)->order;
        return RedsysPaymentGateway::get()->charge($chargeRequest, $order);
    }
}
