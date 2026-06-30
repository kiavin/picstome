@props([
    'placeholder' => __('Search...'),
    'autofocus'   => false,
    'closable'    => false,
])

{{--
    Flux Pro Polyfill: <flux:command.input>
    Search input rendered at the top of the command palette.
    Supports wire:model.live for real-time search, autofocus, and an
    optional close (×) button that clears the input value.
--}}
<div class="relative flex items-center border-b border-zinc-200 dark:border-zinc-700">
    {{-- Search icon --}}
    <div class="pointer-events-none absolute left-3.5 flex items-center">
        <svg class="size-4 text-zinc-400 dark:text-zinc-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 15.803a7.5 7.5 0 0 0 10.607 0Z" />
        </svg>
    </div>

    <input
        type="text"
        placeholder="{{ $placeholder }}"
        {{ $autofocus ? 'autofocus' : '' }}
        {{ $attributes->merge([
            'class' => 'w-full bg-transparent py-3.5 pl-10 pr-10 text-sm text-zinc-900 placeholder-zinc-400 outline-none dark:text-zinc-100 dark:placeholder-zinc-500',
        ]) }}
    />

    @if ($closable)
        <div class="absolute right-3 flex items-center">
            <button
                type="button"
                x-show="$wire.search !== null && $wire.search !== ''"
                x-cloak
                @click="$wire.search = null"
                class="rounded p-0.5 text-zinc-400 transition hover:text-zinc-600 focus:outline-none dark:text-zinc-500 dark:hover:text-zinc-300"
                aria-label="{{ __('Clear search') }}"
            >
                <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    @endif
</div>
