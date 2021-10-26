<?php

namespace Revosystems\RedsysPayment\Http\Livewire;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Revosystems\RedsysPayment\Interfaces\PaymentHandler;
use Revosystems\RedsysPayment\Models\ChargeRequest;
use Revosystems\RedsysPayment\Models\Redsys;
use Revosystems\RedsysPayment\Services\RedsysError;

class Form extends Component
{
    protected $listeners = [
        'onFormErrorReceived',
        'onFormSuccess',
        'tokenizedCards.found' => 'shouldSelect',
        'tokenizedCards.payWithCard' => 'payWithCard',
        'onPaymentCompleted',
    ];

    public $shouldSaveCard = false;
    public $orderReference;
    public $customerToken;
    public $cardId;
    protected $iframeUrl;
    protected $merchantCode;
    protected $merchantTerminal;
    protected $buttonText;
    protected $select = false;

    public $formError;
    /**
     * @var mixed
     */
    public $error;

    public function mount($iframeUrl, $merchantCode, $merchantTerminal, $orderReference, $buttonText, $customerToken, $cardId)
    {
        $this->iframeUrl        = $iframeUrl;
        $this->merchantCode     = $merchantCode;
        $this->merchantTerminal = $merchantTerminal;
        $this->orderReference   = $orderReference;
        $this->buttonText       = $buttonText;
        $this->customerToken    = $customerToken;
        $this->cardId           = $cardId;
        $this->formError        = null;
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

    public function onFormSuccess($idOper, $params)
    {
        dd('succes');
        $redsys         = Redsys::get();
        $paymentHandler = $redsys->getPaymentHandler($this->orderReference);
        $chargeRequest  = ChargeRequest::make($this->orderReference, $idOper, $this->cardId, $this->shouldSaveCard, $this->customerToken, $params);
        $result         = $redsys->charge($chargeRequest, $paymentHandler->order());

        $paymentHandler->extraInfo['orderReference'] = $this->orderReference;
        Log::debug("[REDSYS] Serializing handler for order id {$this->orderReference} with value:" . json_encode($paymentHandler));
        Cache::put("rv-redsys-payment.orders.{$this->orderReference}", serialize($paymentHandler), now()->addMinutes(30));

        //        $this->paymentReference = $result->reference;
        $this->emit('payResponse', $result->gatewayResponse);    // Gateway render Javascript handles this result
//        $this->emit('onPayPressed', serialize(ChargeRequest::make($this->orderReference, $idOper, $this->cardId, $this->shouldSaveCard, $this->customerToken, $params)));
    }

    public function onPaymentCompleted($error = null)
    {
        Redsys::get()->getPaymentHandler($this->orderReference)->onPaymentCompleted($error);
    }

    public function shouldSelect(bool $select)
    {
        $this->select = $select;
    }

    public function payWithCard($cardId)
    {
        $redsys = Redsys::get();
        $result = $redsys->charge(ChargeRequest::make($this->orderReference, null,
        $cardId, false, $this->customerToken, []), $redsys->getPaymentHandler($this->orderReference)->order());
        $this->emit('payResponse', $result);
    }
}
