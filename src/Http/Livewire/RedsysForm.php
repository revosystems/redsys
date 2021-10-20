<?php

namespace Revosystems\RedsysGateway\Http\Livewire;

use Livewire\Component;
use Revosystems\RedsysGateway\Models\ChargeRequest;
use Revosystems\RedsysGateway\RedsysError;

class RedsysForm extends Component
{
    protected $listeners = [
        'onFormErrorReceived',
        'onFormSuccess',
        'checkoutButtons.cardsFound' => 'shouldSelect',
    ];

    public $iframeUrl;
    public $merchantCode;
    public $merchantTerminal;
    public $orderId;
    public $buttonText;
    public $customerToken;
    public $shouldSaveCard;
    public $cardId;
    public $select = false;

    public $formError;
    /**
     * @var mixed
     */
    public $error;

    public function mount($iframeUrl, $merchantCode, $merchantTerminal, $orderId, $buttonText, $customerToken, $cardId)
    {
        $this->iframeUrl        = $iframeUrl;
        $this->merchantCode     = $merchantCode;
        $this->merchantTerminal = $merchantTerminal;
        $this->orderId          = $orderId;
        $this->buttonText       = $buttonText;
        $this->customerToken    = $customerToken;
        $this->cardId           = $cardId;
        $this->formError        = null;
    }

    public function render()
    {
        return view('livewire.redsys-form');
    }

    public function onFormErrorReceived($formErrorCode)
    {
        $this->formError = RedsysError::getMessageFromError($formErrorCode);
        $this->emit('showError', $this->formError);
    }

    public function onFormSuccess($idOper, $params)
    {
        $this->emit('onPayPressed', serialize(new ChargeRequest($idOper, $this->cardId, $this->orderId, $this->shouldSaveCard, $this->customerToken, $params)));
    }

    public function shouldSelect(bool $select)
    {
        $this->select = $select;
    }
}
