@extends('layouts.app')

@section('head')
{{--    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.8.2/dist/alpine.min.js" defer></script>--}}
{{--    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>--}}
{{--    @livewireStyles--}}
@endsection

@section('header')
    <div class="flex justify-center">
        <div class="text-sm">Orden #{{$externalOrderId}}</div>
    </div>
@endsection

@section('content')

<div class="w-full flex flex-col space-y-4 h-full" style="height: 100%; min-height: 200px; margin-top: 24px">
    @livewire('form', [
        'iframeUrl'         => $iframeUrl,
        'merchantCode'      => $merchantCode,
        'merchantTerminal'  => $merchantTerminal,
        'orderReference'    => $orderReference,
        'buttonText'        => $buttonText,
    ])

    @livewire('check-status', ['orderReference' => $orderReference])
    @livewire('apple-pay-button')
    @livewire('google-pay-button')
</div>

{{--    @livewireScripts--}}

@endsection