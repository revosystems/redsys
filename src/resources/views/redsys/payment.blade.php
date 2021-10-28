@extends(config('redsys-payment.indexLayout'))

@section('head')
{{--    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.8.2/dist/alpine.min.js" defer></script>--}}
{{--    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>--}}
{{--    @livewireStyles--}}
@endsection

@section('header')
    <div class="flex justify-center">
        <div class="text-sm">Orden #{{$orderId}}</div>
    </div>
@endsection

@section('content')

<div class="w-full flex flex-col space-y-4 h-full" style="height: 100%; min-height: 200px; margin-top: 24px">
    @livewire('tokenized-cards', compact('orderReference', 'customerToken', 'cards'))
    @livewire('form', [
        'iframeUrl'         => $iframeUrl,
        'merchantCode'      => $merchantCode,
        'merchantTerminal'  => $merchantTerminal,
        'orderReference'    => $orderReference,
        'buttonText'        => $buttonText,
        'customerToken'     => $customerToken,
        'isSelected'        => $cards->isEmpty()
    ])

    @livewire('check-status', compact('orderReference'))
    @livewire('apple-pay-button')
    @livewire('google-pay-button')
</div>

{{--    @livewireScripts--}}

@endsection

{{--@push('inner-scripts')--}}
{{--    <script>--}}
{{--        function getCustomerToken(account, store) {--}}
{{--            let key = account + "_" + store--}}
{{--            let customerToken = getCookie(key)--}}
{{--            if (customerToken == null || customerToken === "" || customerToken === "undefined") {--}}
{{--                customerToken = randomString(16)--}}
{{--            }--}}
{{--            const expires = new Date().addDays(120)--}}
{{--            document.cookie = key + "=" + customerToken + "; expires=" + expires.toUTCString() + ";path=/";--}}
{{--            return customerToken;--}}
{{--        }--}}

{{--        function getCookie(cname) {--}}
{{--            var name = cname + "=";--}}
{{--            var ca = document.cookie.split(';');--}}
{{--            for(var i = 0; i < ca.length; i++) {--}}
{{--                var c = ca[i];--}}
{{--                while (c.charAt(0) == ' ') {--}}
{{--                    c = c.substring(1);--}}
{{--                }--}}
{{--                if (c.indexOf(name) == 0) {--}}
{{--                    return c.substring(name.length, c.length);--}}
{{--                }--}}
{{--            }--}}
{{--            return "";--}}
{{--        }--}}

{{--        function randomString(length) {--}}
{{--            let result = '';--}}
{{--            const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';--}}
{{--            const charactersLength = characters.length;--}}
{{--            for (let i = 0; i < length; i++ ) {--}}
{{--                result += characters.charAt(Math.floor(Math.random() * charactersLength));--}}
{{--            }--}}
{{--            return result;--}}
{{--        }--}}

{{--        Date.prototype.addDays = function(days) {--}}
{{--            let date = new Date(this.valueOf());--}}
{{--            date.setDate(date.getDate() + days);--}}
{{--            return date;--}}
{{--        }--}}
{{--    </script>--}}
{{--@endpush--}}