<div class="w-full flex flex-col space-y-4 h-full" style="height: 100%; min-height: 200px; margin-top: 24px">
{{--    @if(!$cardId)--}}
{{--        @livewire('checkout-buttons')--}}
{{--    @endif--}}
    @livewire('redsys-form', [
        'iframeUrl'         => $iframeUrl,
        'merchantCode'      => $merchantCode,
        'merchantTerminal'  => $merchantTerminal,
        'orderId'           => $orderId,
        'buttonText'        => $buttonText,
        'customerToken'     => $customerToken,
        'cardId'            => $cardId,
    ])
    @livewire('redsys-check-status', ['orderId' => $orderId])
{{--    @if(!$cardId)--}}
{{--        @livewire('webapp.billing.apple-pay-button')--}}
{{--        @livewire('webapp.billing.google-pay-button')--}}
{{--    @endif--}}
</div>
