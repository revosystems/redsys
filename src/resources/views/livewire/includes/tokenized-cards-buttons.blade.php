<div>
    <div class="hidden w-full bg-customGray-F1F1F1 container rounded outline-none mx-auto flex-row justify-center items-center pt-10 pb-10 h-auto">
        <div class="la-ball-clip-rotate la-2x w-full justify-center mx-auto" style="color: #4A4A4A">
            <div></div>
        </div>
        <br>
        <h2 class="text-center justify-center mx-auto" style="color: #4A4A4A"> {{ __(config('redsys-payment.translationsPrefix') . 'loadingCards')}} </h2>
    </div>

    <div class="@if (! $hasCards) hidden @endif flex w-full flex-col space-y-4 justify-center md:justify-start">
            @foreach($cards ?? [] as $card)
{{--                @if (App\Services\Pay\LivewirePayHandler::fromSession() instanceof App\Services\Pay\Solo\SoloHandler)--}}
                @include('redsys-payment::livewire.includes.tokenized-card-button', ['card' => $card, 'select' => $loop->first])
{{--                @else--}}
{{--                    @include('webapp.billing.xpress-gateway-card', ['card' => $card])--}}
{{--                @endif--}}
            @endforeach
    </div>
</div>
