<?php

namespace Revosystems\RedsysPayment\Http\Livewire;

use Livewire\Component;
use Revosystems\RedsysPayment\Models\CardsTokenizable;
use Revosystems\RedsysPayment\Models\PaymentHandler;
use Revosystems\RedsysPayment\Models\ChargeRequest;
use Revosystems\RedsysPayment\Models\RedsysPaymentGateway;
use Revosystems\RedsysPayment\Services\RedsysError;

class Form extends Component
{
    protected $listeners = [
        'onFormErrorReceived',
        'onFormSuccess',
        'tabSelected',
        'tokenizedCards.payWithCard' => 'payWithCard',
//        'onPaymentCompleted',
    ];

    public $shouldSaveCard = false;
    public $orderReference;
    public $customerToken;
    public $cardId;
    protected $iframeUrl;
    protected $merchantCode;
    protected $merchantTerminal;
    protected $buttonText;
    protected $isSelected = false;

    public $formError;
    public $error;

    public function mount($iframeUrl, $merchantCode, $merchantTerminal, $orderReference, $buttonText, $customerToken, $isSelected)
    {
        $this->iframeUrl        = $iframeUrl;
        $this->merchantCode     = $merchantCode;
        $this->merchantTerminal = $merchantTerminal;
        $this->orderReference   = $orderReference;
        $this->buttonText       = $buttonText;
        $this->customerToken    = $customerToken;
        $this->formError        = null;
        $this->isSelected       = $isSelected;
    }

    public function render()
    {
        return view('redsys-payment::livewire.form');
    }

    public function onFormErrorReceived($formErrorCode)
    {
        $this->formError = RedsysError::getMessageFromError($formErrorCode);
        $this->emit('showError', $this->formError);
    }

    public function onFormSuccess($operationId, $params)
    {
        $paymentHandler = PaymentHandler::get($this->orderReference);
        $chargeRequest  = ChargeRequest::make($this->orderReference, $operationId, $this->cardId, $this->shouldSaveCard, $this->customerToken, $params);
        $this->emit('payResponse', RedsysPaymentGateway::get()->charge($chargeRequest, $paymentHandler->order)->gatewayResponse);    // Gateway render Javascript handles this result
    }

    public function payWithCard($cardId)
    {
        $chargeRequest = ChargeRequest::make($this->orderReference, null, $cardId, false, $this->customerToken, []);
        $order = PaymentHandler::get($this->orderReference)->order;
        $result = RedsysPaymentGateway::get()->charge($chargeRequest, $order);
        $this->emit('payResponse', $result->gatewayResponse);
    }
}
