<x-redsys-radio-selector :id="$card->id" :name="'mode'" :label="__(config('redsys.translationsPrefix') . 'payWith') . ' ' . $card->alias"
                         :rightLabel="__(config('redsys.translationsPrefix') . 'expirationDate') . ': ' . substr($card->expiration, 2, 4) . '/' . substr($card->expiration, 0, 2)"
                         :selected="$selected">
    <a onclick="onTokenizedCardPressed('{{ $card->id }}')" class="relative cursor-pointer block w-full h-12 flex-row justify-center text-center items-center rounded bg-black">
        <div class="text-white text-md align-middle pt-3 uppercase font-bold">@icon(credit-card)&nbsp;&nbsp; {{__(config('redsys.translationsPrefix') . 'pay')}} {!! $price !!}</div>
    </a>
</x-redsys-radio-selector>
