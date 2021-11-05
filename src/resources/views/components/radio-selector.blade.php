<div id="{{ $id }}" @if($selected) x-data="{ show: true }" @else x-data="{ show: false }" @endif
  x-on:click="show = true; document.getElementById('{{ $id }}-selector').checked = true"
  x-on:click.away="show = false; document.getElementById('{{ $id }}-selector').checked = false"
  class="@if($hidden) hidden @endif w-full max-w-md mx-auto bg-white radio-selector-box shadow-sm">
    <div class="space-x-2 p-4 border-b">
        <input id="{{ $id }}-selector" @if($hideInput) class="hidden" @endif type="radio" name="{{ $name }}" id="{{ $id }}" x-on:click="show = true" @if($selected) checked @endif/>
        <label for="{{ $id }}">{{ $label }}</label>
        <span class="text-gray-400 text-xs float-right pt-1">{{ $rightLabel }}</span>
    </div>
    <div x-cloak x-show="show" class="p-4 bg-white rounded-b-xl transition-all duration-100"
        x-transition:enter="ease-out"
        x-transition:enter-start="opacity-0 transform scale-80 -translate-y-6"
        x-transition:enter-end="opacity-100 transform scale-100 translate-y-0"
        x-transition:leave="ease-in"
        x-transition:leave-start="opacity-100 transform scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 transform scale-80 -translate-y-6">
        {{ $slot }}
    </div>
</div>