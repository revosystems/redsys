<?php

namespace Revosystems\RedsysGateway\Http\Livewire;

use Livewire\Component;
use Revosystems\RedsysGateway\Redsys;
use Revosystems\RedsysGateway\RedsysError;

class Form extends Component
{
    protected $listeners = [
        'onFormErrorReceived',
        'onFormSuccess',
        'checkoutButtons.cardsFound' => 'shouldSelect',
    ];

    public $shouldSaveCard;
    protected $iframeUrl;
    protected $merchantCode;
    protected $merchantTerminal;
    protected $orderReference;
    protected $buttonText;
    protected $customerToken;
    protected $cardId;
    protected $select = false;

    public $formError;
    /**
     * @var mixed
     */
    public $error;

    public function mount($iframeUrl, $merchantCode, $merchantTerminal, $orderReference, $buttonText)
    {
        $this->iframeUrl        = $iframeUrl;
        $this->merchantCode     = $merchantCode;
        $this->merchantTerminal = $merchantTerminal;
        $this->orderReference   = $orderReference;
        $this->buttonText       = $buttonText;
        $this->formError        = null;
    }

    public function render()
    {
        return view('redsys-gateway::livewire.form');
    }

    public function onFormErrorReceived($formErrorCode)
    {
        $this->formError = RedsysError::getMessageFromError($formErrorCode);
        $this->emit('showError', $this->formError);
    }

    public function onFormSuccess($idOper, $params)
    {
        $result                 = Redsys::get()->charge($idOper, $params);
        $this->paymentReference = $result->reference;
        $this->emit('payResponse', $result->gatewayResponse);    // Gateway render Javascript handles this result
//        $this->emit('onPayPressed', serialize(new ChargeRequest($idOper, $this->cardId, $this->orderReference, $this->shouldSaveCard, $this->customerToken, $params)));
    }

    public function shouldSelect(bool $select)
    {
        $this->select = $select;
    }
}
