<div class="w-full">
    <div id="errorContainer" class="hidden absolute flex items-center text-center text-white font-bold px-3 py-3 rounded shadow-xl bg-red-600" style="background-color: #e46e6a">
        <p  id="errorMessage" class="flex text-m"> {{ $formError }}</p>
    </div>

    <x-redsys-radio-selector :id="'new-card-mode'" :name="'mode'" :label="__(config('redsys.translationsPrefix') . 'useNewCard')" :selected="$this->isSelected">
        @include('redsys::redsys.iframe', ['iframeUrl' => $this->iframeUrl])
    </x-redsys-radio-selector>
</div>

    <script>
        function merchantValidation(){ return true; }

        window.addEventListener("message", function receiveMessage(event) {
            storeIdOper(event, "token", "errorCode", merchantValidation);
            if (event.data.error != undefined || event.data.idOper == -1) {
                return onError(event.data.error);
            }
            if (event.data.idOper != undefined && event.data.idOper != -1) {
                onSuccess( event.data.idOper );
            }
        });

        function onError(error) {
            console.log(error)
            window.livewire.emit('onFormErrorReceived', error)
        }

        function onSuccess(operationId) {
            window.livewire.emit('onFormSuccess', operationId, {
                'browser_height' : screen.height,
                'browser_width' : screen.width,
                'browser_tz' : (new Date()).getTimezoneOffset(),
                'browser_color_depth' : screen.colorDepth
            })
        }

        function loadRedsysForm() {
            let buttonStyle = 'background-color:#E35732'
            let bodyStyle = ''
            let boxStyle = ''
            let inputsStyle = ''
            getInSiteForm('card-form', buttonStyle, bodyStyle, boxStyle, inputsStyle, "{!!  $this->buttonText !!}",
                "{{ $this->merchantCode }}", "{{ $this->merchantTerminal }}", "{{ $this->orderReference }}", '{{ $this->cardId }}')
        }

        function showError(message, reload = true) {
            console.error(message)
            if (message) {
                document.getElementById("errorMessage").innerHTML = message
            }
            let errorContainer = document.getElementById("errorContainer")
            errorContainer.hidden = false
            // errorContainer.fadeIn()
            if (! reload) { return; }
            setTimeout(function () {
                errorContainer.hidden = true
                // errorContainer.fadeOut()
            }, 3000);
            // document.getElementsByTagName('iframe').innerHTML = null
            document.getElementById('card-form').getElementsByTagName('iframe')[0].remove();
            // $("#card-form > iframe").remove()
            loadRedsysForm();
        }

        function handleResponse(data) {
            console.log(data)
            if (! data || data.result == 'KO') {
                showError("Something went wrong, redirecting…", false)
                return setTimeout(function () {
                    location.reload();
                }, 3000)
            }
            console.log(data.result)
            if (data.result == 'AUT') {
                console.log(data.displayForm);
                document.getElementById("tokenized-cards-section").hidden = true
                document.getElementById("googlePay").innerHTML = data.displayForm
                document.getElementById("googlePay").style.height = '980px'
                document.getElementById("googlePay").click()
                submitForm()
                return;
            }
            console.log('Emiting onPaymentCompleted event')
            window.livewire.emit("onPaymentCompleted")
        }

        // FROM CHALLENGE BLADE
        function submitForm() {
            document.getElementById("redsys_iframe_acs").onload = function() {
                document.getElementById("redsysAcsForm").style.display="none";
                document.getElementById("redsys_iframe_acs").style.display="inline";
            }
            document.getElementById("redsysAcsForm").submit();
        }

        document.addEventListener("DOMContentLoaded", function(event) {
            {{--window.livewire.emit("tokenObtained", '{{ $this->customerToken }}');--}}
            window.livewire.on('showError', function (formError) {
                showError(formError);
            })
            window.livewire.on('payResponse', function (data) {
                handleResponse(data);
            })
            loadRedsysForm()
        })
    </script>

