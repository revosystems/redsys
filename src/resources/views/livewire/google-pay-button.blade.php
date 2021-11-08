<div>
    <x-redsys-radio-selector :id="'google-pay-mode'" :name="'mode'" :label="'Google Pay'">
        <div id="googlePay" class="block w-full h-16 flex-row justify-center text-center items-center outline-none rounded"></div>
    </x-redsys-radio-selector>

</div>

@push('redsys-scripts-stack')

    <script>
        document.addEventListener("DOMContentLoaded", function(event) {
            window.livewire.on('buttons.customerUpdated', function (data) {
                enableGooglePayButton(data)
            })
        });

        /**
         * Define the version of the Google Pay API referenced when creating your
         * configuration
         * @see {@link https://developers.google.com/pay/api/web/reference/request-objects#PaymentDataRequest|apiVersion in PaymentDataRequest}
         */
        const baseRequest = {
            apiVersion: 2,
            apiVersionMinor: 0
        };
        /**
         * Identify your gateway and your site's gateway merchant identifier
         *
         * The Google Pay API response will return an encrypted payment method capable
         * of being charged by a supported gateway after payer authorization
         *
         * @todo check with your gateway on the parameters to pass
         * @see {@link https://developers.google.com/pay/api/web/reference/request-objects#gateway|PaymentMethodTokenizationSpecification}
         */
        const tokenizationSpecification = {
            type: 'PAYMENT_GATEWAY',
            parameters: {
                'gateway': 'redsys',
                'gatewayMerchantId': "{{ $merchantCode ?? 'FIXME' }}"
            }
        };
        /**
         * Card networks supported by your site and your gateway
         *
         * @see {@link https://developers.google.com/pay/api/web/reference/request-objects#CardParameters|CardParameters}
         */
        const allowedCardNetworks = ["AMEX", "DISCOVER", "JCB", "MASTERCARD", "VISA"];
        /**
         * Card authentication methods supported by your site and your gateway
         *
         * @see {@link https://developers.google.com/pay/api/web/reference/request-objects#CardParameters|CardParameters}
         * supported card networks
         */
        const allowedCardAuthMethods = ["PAN_ONLY", "CRYPTOGRAM_3DS"];
        /**
         * Describe your site's support for the CARD payment method and its required
         * fields
         *
         * @see {@link https://developers.google.com/pay/api/web/reference/request-objects#CardParameters|CardParameters}
         */
        const baseCardPaymentMethod = {
            type: 'CARD',
            parameters: {
                allowedAuthMethods: allowedCardAuthMethods,
                allowedCardNetworks: allowedCardNetworks
            }
        };

        /**
         * Describe your site's support for the CARD payment method including optional
         * fields
         *
         * @see {@link https://developers.google.com/pay/api/web/reference/request-objects#CardParameters|CardParameters}
         */
        const cardPaymentMethod = Object.assign(
            {},
            baseCardPaymentMethod,
            {
                tokenizationSpecification: tokenizationSpecification
            }
        );
        /**
         * An initialized google.payments.api.PaymentsClient object or null if not yet set
         *
         * @see {@link getGooglePaymentsClient}
         */
        let paymentsClient = null;

        /**
         * Configure your site's support for payment methods supported by the Google Pay
         * API.
         *
         * Each member of allowedPaymentMethods should contain only the required fields,
         * allowing reuse of this base request when determining a viewer's ability
         * to pay and later requesting a supported payment method
         *
         * @returns {object} Google Pay API version, payment methods supported by the site
         */
        function getGoogleIsReadyToPayRequest() {
            return Object.assign(
                {},
                baseRequest,
                {
                    allowedPaymentMethods: [baseCardPaymentMethod]
                }
            );
        }

        /**
         * Configure support for the Google Pay API
         *
         * @see {@link https://developers.google.com/pay/api/web/reference/request-objects#PaymentDataRequest|PaymentDataRequest}
         * @returns {object} PaymentDataRequest fields
         */
        function getGooglePaymentDataRequest() {
            const paymentDataRequest = Object.assign({}, baseRequest);
            paymentDataRequest.allowedPaymentMethods = [cardPaymentMethod];
            paymentDataRequest.transactionInfo = getGoogleTransactionInfo();
            paymentDataRequest.merchantInfo = {
                // See {@link https://developers.google.com/pay/api/web/guides/test-and-deploy/integration-checklist|Integration checklist}
                merchantId: 'BCR2DN6T275ZLS3R',
                merchantName: 'Revo Systems',
                merchantOrigin: '{{ Illuminate\Support\Str::contains( config('app.name'),'staging') ?  "staging.revointouch.works"  : "solo.revointouch.works" }}'
            };
            return paymentDataRequest;
        }

        /**
         * Return an active PaymentsClient or initialize
         *
         * @see {@link https://developers.google.com/pay/api/web/reference/client#PaymentsClient|PaymentsClient constructor}
         * @returns {google.payments.api.PaymentsClient} Google Pay API client
         */
        function getGooglePaymentsClient() {
            if ( paymentsClient === null ) {
                paymentsClient = new google.payments.api.PaymentsClient({environment: "TEST" });
{{--                paymentsClient = new google.payments.api.PaymentsClient({environment: "{{ app( \App\Billing\WebApp\WebAppPaymentGateway::class)->gatewayService->isTestEnvironment() ? 'TEST' : 'PRODUCTION'}}" });--}}
            }
            return paymentsClient;
        }
        /**
         *
         * Initialize Google PaymentsClient after Google-hosted JavaScript has loaded
         *
         * Display a Google Pay payment button after confirmation of the viewer's
         * ability to pay.
         */
        function onGooglePayLoaded() {
            const paymentsClient = getGooglePaymentsClient();
            paymentsClient.isReadyToPay(getGoogleIsReadyToPayRequest())
                .then(function(response) {
                    if (response.result) {
                        addGooglePayButton();
                        // @todo prefetch payment data to improve performance after confirming site functionality
                        // prefetchGooglePaymentData();
                    }
                })
                .catch(function(err) {
                    // show error in developer console for debugging
                    console.error(err);
                });
        }
        /**
         * Add a Google Pay purchase button alongside an existing checkout button
         *
         * @see {@link https://developers.google.com/pay/api/web/reference/request-objects#ButtonOptions|Button options}
         * @see {@link https://developers.google.com/pay/api/web/guides/brand-guidelines|Google Pay brand guidelines}
         */
        function addGooglePayButton() {
            const paymentsClient = getGooglePaymentsClient();
            const button = paymentsClient.createButton({
                buttonColor: 'white',
                buttonType: 'plain',
                buttonSizeMode: 'fill',
                onClick: onGooglePaymentButtonClicked
            });
            document.getElementById('googlePay').appendChild(button);
            enableGooglePayButton( String(true));
            {{--enableGooglePayButton( String({{ app(App\Services\Solo\SoloServices::class)->customer->validate() ? 'true' : 'false'}}) === 'true');--}}
        }
        /**
         * Provide Google Pay API with a payment amount, currency, and amount status
         *
         * @see {@link https://developers.google.com/pay/api/web/reference/request-objects#TransactionInfo|TransactionInfo}
         * @returns {object} transaction info, suitable for use as transactionInfo property of PaymentDataRequest
         */
        function getGoogleTransactionInfo() {
            return {
                countryCode: 'ES',
                currencyCode: 'EUR',
                totalPriceStatus: 'FINAL',
                // set to cart total
                totalPrice: '23'
                {{--totalPrice: '{{ app(App\Services\Solo\SoloServices::class)->order->total/100 }}'--}}
            };
        }

        /**
         * Prefetch payment data to improve performance
         *
         * @see {@link https://developers.google.com/pay/api/web/reference/client#prefetchPaymentData|prefetchPaymentData()}
         */
        function prefetchGooglePaymentData() {
            const paymentDataRequest = getGooglePaymentDataRequest();
            // transactionInfo must be set but does not affect cache
            paymentDataRequest.transactionInfo = {
                totalPriceStatus: 'NOT_CURRENTLY_KNOWN',
                currencyCode: 'EUR'
            };
            const paymentsClient = getGooglePaymentsClient();
            paymentsClient.prefetchPaymentData(paymentDataRequest);
        }

        /**
         * Show Google Pay payment sheet when Google Pay payment button is clicked
         */
        function onGooglePaymentButtonClicked() {
            const paymentDataRequest = getGooglePaymentDataRequest();
            paymentDataRequest.transactionInfo = getGoogleTransactionInfo();

            const paymentsClient = getGooglePaymentsClient();
            paymentsClient.loadPaymentData(paymentDataRequest)
                .then(function(paymentData) {
                    // handle the response
                    processPayment(paymentData);
                })
                .catch(function(err) {
                    // show error in developer console for debugging
                    console.error(err);
                });
        }
        /**
         * Process payment data returned by the Google Pay API
         *
         * @param {object} paymentData response from Google Pay API after user approves payment
         * @see {@link https://developers.google.com/pay/api/web/reference/response-objects#PaymentData|PaymentData object reference}
         */
        function processPayment(paymentData) {
            // show returned data in developer console for debugging
            console.log(paymentData);
            console.log(paymentData.paymentMethodData.tokenizationData.token);
            paymentToken = paymentData.paymentMethodData.tokenizationData.token;
            window.livewire.emit('onGooglePayAuthorized', paymentToken)
        }

        function enableGooglePayButton(enable){
            const googlePayButton = document.getElementsByClassName("gpay-button")[0];
            if (googlePayButton == null) {
                return;
            }
            googlePayButton.disabled = !enable;
            console.log("GPAY button " + (enable ? 'enabled' : 'disabled'))
        }
    </script>
@endpush