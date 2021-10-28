<script src="{{ $iframeUrl }}"></script>
<div style="max-width:400px;" class="mx-auto space-x-2">
    <input type="checkbox" wire:model="shouldSaveCard" />
    <label class="">{{ __(config('redsys.translationsPrefix') . 'saveCardForPayments') }}</label>
</div>
<div id="card-form" style="height: 300px; margin: auto;" wire:ignore>
    @includeWhen($cardId, 'redsys::livewire.includes.loading-ring')
</div>
<input type="hidden" id="token"/>
<input type="hidden" id="errorCode"/>