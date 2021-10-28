<div id="tokenized-cards-section">
    <div class="@if (! $cards->isNotEmpty()) hidden @endif flex w-full flex-col space-y-4 justify-center md:justify-start">
        @foreach($cards ?? [] as $card)
            @include('redsys::livewire.includes.tokenized-card-button', ['card' => $card, 'select' => $loop->first])
        @endforeach
    </div>
</div>
