<x-redsys-radio-selector :id="$card->id" :name="'mode'" :label="__(config('redsys.translationsPrefix') . 'payWith').' '.$card->alias" :selected="$select">
    <a onclick="onTokenizedCardPressed('{{ $card->id }}')" class="relative cursor-pointer block w-full h-20 flex-row justify-center text-center items-center outline-none px-4 py-4 md:py-4 border border-gray-400 rounded shadow-sm bg-white ">
        <div class="block w-full justify-between justify-center text-center items-center">
            <span class="text-customGray-4A4A4A text-sm">@icon(credit-card) {{__(config('redsys.translationsPrefix') . 'payWith')}} {!! $amount !!} {{ $card->alias }}</span>
        </div>
        <div class="flex justify-center">
            <span class="text-customGray-4A4A4A text-xs">{{ __(config('redsys.translationsPrefix') . 'expirationDate')}} {{ substr($card->expiration, 2, 4) }} / {{ substr($card->expiration, 0, 2) }}</span>
        </div>
    </a>
</x-redsys-radio-selector>
