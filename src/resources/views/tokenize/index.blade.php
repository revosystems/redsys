@extends(config('redsys.indexLayout'))

@section('redsys-head')
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.8.2/dist/alpine.min.js" defer></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    {!! \Khill\FontAwesome\FontAwesome::css() !!}
@endsection

@section('content')

    <div class="w-full flex flex-col space-y-4 h-full" style="height: 100%; min-height: 200px; margin-top: 24px">
        @include('redsys::tokenize.main', [
            'redsysFormId'      => 'redsys-init-form',
            'redsysConfig'      => $redsysConfig,
            'paymentReference'  => $paymentReference,
            'chargePayment'     => $chargePayment,
        ])
    </div>
@endsection