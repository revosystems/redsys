<?php

namespace Revosystems\Redsys\Http\Livewire;

use Livewire\Component;
use Revosystems\Redsys\Models\ChargeRequest;
use Revosystems\Redsys\Models\ChargeResult;
use Revosystems\Redsys\Models\GatewayCard;
use Revosystems\Redsys\Models\PaymentHandler;
use Revosystems\Redsys\Models\RedsysPaymentGateway;
use Revosystems\Redsys\Services\RedsysError;

class Redsys extends Component
{
    protected $listeners = [
        'onCardFormSubmit',
        'onTokenizedCardPressed',
//        'onPaymentCompleted',
    ];

    public $shouldSaveCard = false;
    public $orderReference;
    public $customerToken;
    public $cards;
    public $iframeUrl;
    public $buttonText;
    public $amount;

    protected $merchantCode;
    protected $merchantTerminal;

    public function mount($iframeUrl, $merchantCode, $merchantTerminal, $paymentHandler, $orderReference, $customerToken, $cards)
    {
        $this->iframeUrl        = $iframeUrl;
        $this->merchantCode     = $merchantCode;
        $this->merchantTerminal = $merchantTerminal;
        $this->orderReference   = $orderReference;
        $this->amount           = $paymentHandler->order->price()->format();
        $this->buttonText       = __(config('redsys.translationsPrefix') . 'pay');
        $this->customerToken    = $customerToken;
        $this->cards            = $cards;
    }

    public function render()
    {
        return view('redsys::livewire.redsys');
    }

    public function hydrate()
    {
        $this->cards = collect($this->cards)->map(function ($card) {
            return new GatewayCard($card['id'], $card['alias'], $card['expiration']);
        });
    }

    public function onCardFormSubmit($operationId, $params, $formErrorCode = null)
    {
        $chargeRequest = ChargeRequest::makeWithOperationId($this->orderReference, $operationId, $this->shouldSaveCard, $this->customerToken, $params);
        $this->emit('payResponse', $this->chargeToRedsys($chargeRequest)->gatewayResponse);
    }

    public function onTokenizedCardPressed($cardId)
    {
        $chargeRequest = ChargeRequest::makeWithCard($this->orderReference, $cardId, $this->customerToken, []);
        $this->emit('payResponse', $this->chargeToRedsys($chargeRequest)->gatewayResponse);
    }

    protected function chargeToRedsys(ChargeRequest $chargeRequest) : ChargeResult
    {
        $order = PaymentHandler::get($this->orderReference)->order;
        return RedsysPaymentGateway::get()->charge($chargeRequest, $order);
    }
}
