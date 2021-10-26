<form id="cardPaymentForm" action="{{route('payCheck')}}" method="POST" class="w-full">
    <div class="max-w-md mx-auto">
{{--        <div x-data="payOnlyCard()" x-init="getToken('{{ $account }}')">--}}
        <div>
            <input type="hidden" name="deviceFingerprint" id="fingerprint" />
            <input type="hidden" name="customerToken" id="token"  value="{{ $customerToken }}"/>
{{--            <input type="hidden" name="customerToken" id="token"  x-model="token"/>--}}
            <input type="hidden" name="amount" id="amount"  value="{{ $amount }}"/>
            <input type="hidden" name="cardId" id="cardId"/>
        </div>
        @include('redsys-payment::livewire.includes.tokenized-cards-buttons')
    </div>
</form>

<script>
    // function payOnlyCard(){
    //     return {
    //         token: "",
    //         getToken(account) {
    //             this.token = getCustomerToken(account)
    //         }
    //     }
    // }
    window.addEventListener('proceedToCheckout', event => {
        if (document.getElementById("shouldSaveCard")) {
            document.getElementById("shouldSaveCard").value = event.detail['shouldSaveCard']
        }
        document.getElementById("cardId").value = event.detail['cardId']
        document.getElementById("cardPaymentForm").submit();
    })

    {{--window.livewire.emit("tokenObtained", '{{ $this->customerToken }}');--}}

    {{--getToken();--}}

    {{--async function getToken(){--}}
    {{--    console.log('toke')--}}
    {{--    var token = getCustomerToken("{{ $account }}");--}}
    {{--    await sleep(100);  // This is needed because if is not set event is not triggered or token is null--}}
    {{--    window.livewire.emit("tokenObtained", token );--}}
    {{--}--}}

    function sleep(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }

</script>


{{--@push('inner-scripts')--}}
{{--<script>--}}
{{--</script>--}}
{{--@endpush--}}