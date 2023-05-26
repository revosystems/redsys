<?php

namespace Revosystems\Redsys\Http\Livewire;

use Livewire\Component;
use Revosystems\Redsys\Models\ChargeResult;
use Revosystems\Redsys\Services\RedsysChargePayment;
use Revosystems\Redsys\Services\RedsysChargeRequest;
use Revosystems\Redsys\Models\RedsysPaymentGateway;

class RedsysTokenizeForm extends Component
{
    protected $listeners = [
        'onCardFormSubmit',
        'onTokenizeCompleted',
    ];

    public $paymentReference;
    public $iframeUrl;
    public $redsysFormId;

    public function mount(string $redsysFormId, string $paymentReference)
    {
        $this->redsysFormId     = $redsysFormId;
        $this->paymentReference = $paymentReference;
        $this->iframeUrl        = RedsysPaymentGateway::get()->isTestEnvironment() ? 'https://sis-t.redsys.es:25443/sis/NC/sandbox/redsysV2.js' : 'https://sis.redsys.es/sis/NC/redsysV2.js';
    }

    public function render()
    {
        return view('redsys::livewire.redsys-tokenize-form');
    }

    public function onTokenizeCompleted(ChargeResult  $data) : void
    {
        RedsysChargePayment::get($this->paymentReference)->payHandler->onPaymentSucceed($this->paymentReference, $data);
    }
    
    public function onCardFormSubmit(string $operationId, array $extraInfo) : void
    {
        $chargeRequest = RedsysChargeRequest::makeWithOperationId($this->paymentReference, $operationId, $extraInfo);
        $chargeRequest->customerToken = 'customer_token';
        $this->emit('payResponse', RedsysPaymentGateway::get()->tokenizeCard(
            RedsysChargePayment::get($this->paymentReference),
            $chargeRequest
        )->gatewayResponse);
    }

}
