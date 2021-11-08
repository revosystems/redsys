@extends(config('redsys.indexLayout'))

@section('redsys-head')
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.8.2/dist/alpine.min.js" defer></script>
    {{--    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>--}}
    {!! \Khill\FontAwesome\FontAwesome::css() !!}
    <style>
        .apple-pay-button {
            display: inline-block;
            -webkit-appearance: -apple-pay-button;
            -apple-pay-button-type: pay; /* Use any supported button type. */
        }
        .apple-pay-button-black {
            -apple-pay-button-style: black;
        }
        .apple-pay-button-white-with-line {
            -apple-pay-button-style: white-outline;
        }
    </style>
    @livewireStyles
@endsection

@section('header')
    <div class="flex justify-center">
        <div class="text-sm">Orden #{{$chargePayment->externalReference}}</div>
    </div>
@endsection

@section('content')

    <div class="w-full flex flex-col space-y-4 h-full" style="height: 100%; min-height: 200px; margin-top: 24px">
        @include('redsys::redsys.main', [
            'redsysFormId'      => 'redsys-init-form',
            'redsysConfig'      => $redsysConfig,
            'chargePayment'     => $chargePayment,
            'customerToken'     => $customerToken,
            'cards'             => $cards,
        ])
    </div>

@endsection

@push('redsys-scripts-stack')
    @livewireScripts
    <script async src="https://pay.google.com/gp/p/js/pay.js" onload="onGooglePayLoaded()"></script>
@endpush
