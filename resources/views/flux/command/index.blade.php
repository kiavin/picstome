@props([])

{{--
    Flux Pro Polyfill: <flux:command>
    Command palette wrapper — a styled, scrollable container used as the
    root element of the search / command palette UI.
--}}
<div
    {{ $attributes->merge([
        'class' => 'relative w-full overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-lg dark:border-zinc-700 dark:bg-zinc-900',
    ]) }}
>
    {{ $slot }}
</div>
