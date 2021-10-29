<div class="@if (! $cards->isNotEmpty()) hidden @endif flex w-full flex-col space-y-4 justify-center md:justify-start">
    @foreach($cards as $card)
        @include('redsys::app.cards.tokenized-card', ['card' => $card, 'selected' => $loop->first])
    @endforeach
</div>
