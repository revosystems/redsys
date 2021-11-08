<div>
    <x-redsys-radio-selector :id="'apple-pay-mode'" :name="'mode'" :label="'Apple Pay'">
        <button type="button" id="applePay" class="apple-pay-button apple-pay-button-white-with-line block w-full h-16 flex-row justify-center text-center items-center outline-none p-4 mb-1 rounded" onclick="onApplePayClicked()"></button>
    </x-redsys-radio-selector>
</div>

@push('redsys-scripts-stack')
    <script src="https://applepay.cdn-apple.com/jsapi/v1/apple-pay-sdk.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function(event) {
            // Check that client is connected using Safari
            checkApplePayAvailability();

            window.addEventListener('onTokenizedCardPressed', event => {
                // document.getElementById("cardPaymentForm").submit();
            })

            window.livewire.on('applePayPaymentCompleted', function (data) {
                if (data == 'SUCCESS'){
                    // If transaction is correct (0), show success on modal
                    session.completePayment(ApplePaySession.STATUS_SUCCESS);
                    return;
                }
                // If transaction is incorrect, show error on modal
                session.completePayment(ApplePaySession.STATUS_FAILURE);
            })

            window.livewire.on('buttons.customerUpdated', function (data) {
                enableApplePayButton(data)
            })
        });

        var session;
        function onApplePayClicked() {
            // Order data
            var paymentRequest = createPaymentRequest();
            // Apple Pay version to use
            session = new ApplePaySession(1, paymentRequest);
            // Send session petition
            // check store token is valid
            session.onvalidatemerchant = (event) => {
                axios.post("/applePay/redsys", {"validationUrl" : event.validationURL}, function(data){
                    session.completeMerchantValidation(data)
                }, "json").then(function(res){
                    console(res.status)
                    console.log("Apple Pay post")
                }).catch(function(){
                    console.log("Apple Pay Validate merchant error")
                });
            };
            // session.oncancel when user cancels the payment mode
            session.oncancel = (event) => {
                console.log("Apple Pay Session onCancel")
            };
            // Show Apple Pay modal
            session.begin();

            session.onpaymentauthorized = (event) => {
                const token = event.payment.token.paymentData;
                onApplePayAuthorized(JSON.stringify(token))
            };
        }

        function createPaymentRequest() {
            return {
                currencyCode: 'EUR', // TODO use store currency
                countryCode: 'ES',
                total: {
                    label: 'Pago en ' + 'FAKE STORE NAME FIX',
                    amount: 'FAKE TOTAL FIX'
                },
                supportedNetworks: ['visa', 'masterCard', 'amex', 'discover'],
                merchantCapabilities: ['supports3DS', 'supportsCredit', 'supportsDebit']
            };
        }
        function checkApplePayAvailability() {
            // $('#applePay').hide()
            if(window.ApplePaySession) {
                var merchantIdentifier = 'merchant.com.redsys.revointouch';
                var promise = ApplePaySession.canMakePaymentsWithActiveCard(merchantIdentifier);
                promise.then(function(canMakePayments) {
                    if (canMakePayments) { document.getElementById('applePay').show(); }
                }).finally( function() {
{{--                    enableApplePayButton(String({{ solo()->customer->validate() ? 'true' : 'false'}}) === 'true');--}}
                    enableApplePayButton(true);
                });
            } else {
                document.getElementById('applePay').hidden = true
                document.getElementById('apple-pay-mode').hidden = true
            }
        }

        function onApplePayAuthorized(data) {
            window.livewire.emit('onApplePayAuthorized', data)
        }

        function enableApplePayButton(enable){
            const applePayButton = document.getElementsByClassName("apple-pay-button")[0];
            if (applePayButton == null) {
                return;
            }
            applePayButton.disabled = !enable;
        }

    </script>
@endpush