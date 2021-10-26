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
        'checkoutButtons.cardsFound' => 'shouldSelect',
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
        return view('redsys-payment::livewire.form');
    }

    public function onFormErrorReceived($formErrorCode)
    {
        $this->formError = RedsysError::getMessageFromError($formErrorCode);
        $this->emit('showError', $this->formError);
    }

    public function onFormSuccess($idOper, $params)
    {
        $redsys = Redsys::get();
        $paymentHandler = $this->getPaymentHandler($redsys);
        $chargeRequest  = ChargeRequest::make($this->orderReference, $idOper, $this->cardId, $this->shouldSaveCard, $this->customerToken, $params);
        $result = $redsys->charge($chargeRequest, $paymentHandler->order());

        $paymentHandler->extraInfo['orderReference'] = $this->orderReference;
        Log::debug("[REDSYS] Serializing handler for order id {$this->orderReference} with value:" . json_encode($paymentHandler));
        Cache::put("rv-redsys-payment.orders.{$this->orderReference}", serialize($paymentHandler), now()->addMinutes(30));

        //        $this->paymentReference = $result->reference;
        $this->emit('payResponse', $result->gatewayResponse);    // Gateway render Javascript handles this result
//        $this->emit('onPayPressed', serialize(ChargeRequest::make($this->orderReference, $idOper, $this->cardId, $this->shouldSaveCard, $this->customerToken, $params)));
    }

    public function onPaymentCompleted($error = null)
    {
        $this->getPaymentHandler(Redsys::get())->onPaymentCompleted($error);
    }

    public function shouldSelect(bool $select)
    {
        $this->select = $select;
    }

    protected function getPaymentHandler($redsys) : PaymentHandler
    {
        return $redsys->paymentHandlerClass::get($this->orderReference);
    }
}
