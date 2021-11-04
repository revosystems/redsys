@extends(config('redsys.indexLayout'))

@section('redsys-head')
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.8.2/dist/alpine.min.js" defer></script>
{{--    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>--}}
    {!! \Khill\FontAwesome\FontAwesome::css() !!}
    @livewireStyles
@endsection

@section('header')
    <div class="flex justify-center">
        <div class="text-sm">Orden #{{$externalReference}}</div>
    </div>
@endsection

@section('content')

<div class="w-full flex flex-col space-y-4 h-full" style="height: 100%; min-height: 200px; margin-top: 24px">
    @include('redsys::redsys.main', [
        'redsysFormId'      => 'redsys-init-form',
        'redsysConfig'      => $redsysConfig,
        'price'             => $price,
        'paymentReference'  => $paymentReference,
        'customerToken'     => $customerToken,
        'cards'             => $cards,
    ])
</div>

@endsection

@push('redsys-scripts-stack')
    @livewireScripts
@endpush