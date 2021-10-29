@extends(config('redsys.indexLayout'))

@section('head')
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.8.2/dist/alpine.min.js" defer></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    @livewireStyles
@endsection

@section('header')
    <div class="flex justify-center">
        <div class="text-sm">Orden #{{$orderId}}</div>
    </div>
@endsection

@section('content')

<div class="w-full flex flex-col space-y-4 h-full" style="height: 100%; min-height: 200px; margin-top: 24px">
    @livewire('redsys', [
        'iframeUrl'         => $iframeUrl,
        'merchantCode'      => $merchantCode,
        'merchantTerminal'  => $merchantTerminal,
        'paymentHandler'    => $paymentHandler,
        'orderReference'    => $orderReference,
        'customerToken'     => $customerToken,
        'cards'             => $cards
    ])

</div>

@endsection

@section('scripts')
    @livewireScripts
@stop