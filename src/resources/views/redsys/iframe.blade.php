<script src="{{ $iframeUrl }}"></script>
<div style="max-width:400px;" class="mx-auto space-x-2 mb-4">
    <input type="checkbox" wire:model="shouldSaveCard" />
    <label>{{ __(config('redsys.translationsPrefix') . 'saveCardForPayments') }}</label>
</div>
<div id="{{ $redsysFormId }}" style="height: 300px; margin: auto;" wire:ignore>
</div>
<input type="hidden" id="token"/>
<input type="hidden" id="errorCode"/>