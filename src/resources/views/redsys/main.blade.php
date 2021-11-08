<div class="flex w-full flex-col space-y-4 justify-center md:justify-start">
    <div id="errorContainer" class="hidden flex items-center text-center text-white font-bold px-3 py-3 rounded shadow-xl bg-red-600" style="background-color: #e46e6a">
        <p id="errorMessage" class="flex text-m"></p>
    </div>
    <div>
        @include('redsys::app.cards.tokenized-cards', compact('cards'))
    </div>

    @livewire('redsys-form', array_merge(
        compact('redsysFormId', 'paymentReference', 'price', 'customerToken'),
        ['hasCards' => $cards->isNotEmpty()]
    ))

    @livewire('check-status', compact('paymentReference'))

    @livewire('apple-pay-button', compact('paymentReference'))

    @livewire('google-pay-button', compact('paymentReference'))

    <x-redsys-radio-selector :id="'challenge-form-box'" :name="'challenge-form'" :label="'Redsys'" :hidden="true" :hideInput="true">
        <div id="challenge-form" class="block w-full h-16 flex-row justify-center text-center items-center outline-none rounded"></div>
    </x-redsys-radio-selector>
</div>

<script>
    function loadRedsysForm(inputsStyle = '') {
        buttonStyle = 'background-color:#000000; margin-top:28px; margin-bottom: -28px; width:310px; height:48px; text-transform: uppercase; margin-right:0; margin-left:0;' +
            'border-radius: 0.25rem;' +
            'font-size:16px; font-weight: 700;' +
            'font-family: ui-sans-serif, system-ui, -apple-system, "system-ui", "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji"';
        boxStyle = 'box-shadow:none; margin-top:24px'
        bodyStyle = 'margin-top: -68px; color: gray'
        // Redsys iframe available method to load card form
        getInSiteForm('{{ $redsysFormId }}', buttonStyle, bodyStyle, boxStyle, inputsStyle, '{!! __(config('redsys.translationsPrefix') . 'pay') . ' ' . $price !!}',
            "{{ $redsysConfig->code }}", "{{ $redsysConfig->terminal }}", "{{ $paymentReference }}", false)
    }

    function showError(message) {
        console.error(message)
        if (message) {
            document.getElementById("errorMessage").innerHTML = message
        }
        document.getElementById("errorContainer").classList.remove('hidden')
        setTimeout(function () {
            document.getElementById("errorContainer").classList.add('hidden')
        }, 3000);
    }

    function onTokenizedCardPressed(cardId) {
        window.livewire.emit('onTokenizedCardPressed', cardId, browserData())
    }

    function browserData() {
        return {
            'browser_height' : screen.height,
            'browser_width' : screen.width,
            'browser_tz' : (new Date()).getTimezoneOffset(),
            'browser_color_depth' : screen.colorDepth
        }
    }

    function handleResponse(data) {
        console.log(data)
        if (! data || ['OK', 'AUT'].indexOf(data.result) === -1) {
            showError("Something went wrong, redirecting…")
            return setTimeout(function () {
                location.reload();
            }, 3000)
        }
        if (data.result === 'AUT') {
            loadChallengeForm(data.displayForm)
            submitChallengeForm()
        }
        if (data.result === 'OK') {
            window.livewire.emit("onPaymentCompleted")
        }
    }

    function loadChallengeForm(displayForm) {
        console.debug(displayForm);
        Array.from(document.getElementsByClassName('radio-selector-box')).forEach((el) => { el.classList.contains('hidden') ? el.classList.remove('hidden') : el.classList.add('hidden') })
        document.getElementById("challenge-form-box").classList.remove('hidden')
        document.getElementById("challenge-form").style.height = '980px'
        document.getElementById("challenge-form").innerHTML = displayForm
        document.getElementById("challenge-form").click()
    }

    function submitChallengeForm() {
        document.getElementById("redsys_iframe_acs").onload = function() {
            document.getElementById("redsysAcsForm").style.display = "none";
            document.getElementById("redsys_iframe_acs").style.display = "inline";
        }
        document.getElementById("redsysAcsForm").submit();
    }

    // Redsys iframe triggered event
    window.addEventListener("message", function receiveMessage(event) {
        // Redsys iframe available method to validate card submitted event was received
        storeIdOper(event, "token", "errorCode", function merchantValidation() { return true });
        if (event.data.error || event.data.idOper === -1) {
            showError(redsysErrors[event.data.error] ?? 'Redsys error');
            return;
        }
        if (event.data.idOper && event.data.idOper !== -1) {
            window.livewire.emit('onCardFormSubmit', event.data.idOper, browserData())
        }
    });

    document.addEventListener("DOMContentLoaded", function(event) {
        window.livewire.on('payResponse', function (data) {
            handleResponse(data);
        })
        loadRedsysForm()
    })

    let redsysErrors = {
        "msg1": "Ha de rellenar los datos de la tarjeta",
        "msg2": "La tarjeta es obligatoria",
        "msg3": "La tarjeta ha de ser numérica",
        "msg4": "La tarjeta no puede ser negativa",
        "msg5": "El mes de caducidad de la tarjeta es obligatorio",
        "msg6": "El mes de caducidad de la tarjeta ha de ser numérico",
        "msg7": "El mes de caducidad de la tarjeta es incorrecto",
        "msg8": "El año de caducidad de la tarjeta es obligatorio",
        "msg9": "El año de caducidad de la tarjeta ha de ser numérico",
        "msg10": "El año de caducidad de la tarjeta no puede ser negativo",
        "msg11": "El código de seguridad de la tarjeta no tiene la longitud correcta",
        "msg12": "El código de seguridad de la tarjeta ha de ser numérico",
        "msg13": "El código de seguridad de la tarjeta no puede ser negativo",
        "msg14": "El código de seguridad no es necesario para su tarjeta",
        "msg15": "La longitud de la tarjeta no es correcta",
        "msg16": "Debe Introducir un número de tarjeta válido (sin espacios ni guiones).",
        "msg17": "Validación incorrecta por parte del comercio"
    }
</script>