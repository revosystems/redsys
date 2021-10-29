<div wire:ignore
     class="@if (! $cards->isNotEmpty()) hidden @endif flex w-full flex-col space-y-4 justify-center md:justify-start"
>
    @foreach($cards as $card)
        @include('redsys::livewire.includes.tokenized-card', ['card' => $card, 'selected' => $loop->first])
    @endforeach
</div>
