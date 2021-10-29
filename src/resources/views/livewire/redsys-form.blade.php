<x-redsys-radio-selector :id="'new-card-mode'" :name="'mode'" :label="__(config('redsys.translationsPrefix') . 'useNewCard')" :selected="!$hasCards">
    @include('redsys::redsys.iframe', compact('iframeUrl'))
</x-redsys-radio-selector>
