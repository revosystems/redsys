<?php

namespace Revosystems\RedsysPayment\Http\Livewire;

use Livewire\Component;
use Revosystems\RedsysPayment\Models\CardsTokenizable;
use Revosystems\RedsysPayment\Models\PaymentHandler;
use Revosystems\RedsysPayment\Models\GatewayCard;

class TokenizedCards extends Component
{
    public $cards            = null;
    public $customerToken;

    public $account;
    public $amount;
    public $orderReference;

    public function mount($orderReference, $customerToken, $cards)
    {
        $this->orderReference = $orderReference;
        $this->customerToken = $customerToken;
        $paymentHandler     = PaymentHandler::get($orderReference);
        $this->account      = $paymentHandler->account;
        $this->amount       = $paymentHandler->order->price()->format();
        $this->cards        = $cards;
    }

    public function render()
    {
        return view('redsys-payment::livewire.tokenized-cards');
    }

    public function hydrate()
    {
        $this->cards = collect($this->cards)->map(function ($card) {
            return new GatewayCard($card['id'], $card['alias'], $card['expiration']);
        });
    }

    public function payWithCard($cardId)
    {
        $this->emit('tokenizedCards.payWithCard', $cardId);
    }
}
