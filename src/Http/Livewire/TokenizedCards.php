<?php

namespace Revosystems\RedsysPayment\Http\Livewire;

use Livewire\Component;
use Revosystems\RedsysPayment\Models\GatewayCard;
use Revosystems\RedsysPayment\Models\Redsys;

class TokenizedCards extends Component
{
    public $cards            = null;
    public $hasCards         = true;
    public $customerToken;

    protected $listeners = ['tokenObtained'];

    public function mount($orderReference, $customerToken)
    {
        $paymentHandler = Redsys::get()->getPaymentHandler($orderReference);
        $this->account   = $paymentHandler->account();
        $this->amount    = $paymentHandler->order()->price()->amount;
        $this->customerToken = $customerToken;
//        LivewirePayHandler::fromSession()->registerPaymentGateway();
    }

    public $account;
    public $amount;

    public function render()
    {
//        $this->updateCanProccedToCheckout();
//        LivewirePayHandler::fromSession()->registerPaymentGateway();
        return view('redsys-payment::livewire.tokenized-cards');
    }

    public function hydrate()
    {
        $this->cards = collect($this->cards)->map(function ($card) {
            return new GatewayCard($card['id'], $card['alias'], $card['expiration']);
        });
    }

    public function proceedToCheckout($shouldSaveCard = false)
    {
        dd('is it required');
        $this->dispatchBrowserEvent('proceedToCheckout', compact('shouldSaveCard'));
    }

    public function payWithCard($cardId)
    {
        $this->emit('tokenizedCards.payWithCard', $cardId);
//        $this->dispatchBrowserEvent('proceedToCheckout', compact('cardId'));
    }

    public function tokenObtained($customerToken)
    {
        $this->cards        = Redsys::get()->getCardsForCustomer($customerToken);
        $this->hasCards     = $this->cards->isNotEmpty();
        $this->emit('tokenizedCards.found', ! $this->hasCards);
    }
}
