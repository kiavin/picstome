@props([])

{{--
    Flux Pro Polyfill: <flux:command.item>
    A single result row in the command palette.
    Forwards all attributes (wire:click, etc.) to the root button element.
--}}
<button
    type="button"
    role="option"
    {{ $attributes->merge([
        'class' => 'flex w-full cursor-pointer items-center gap-2 rounded-lg px-3 py-2 text-sm text-zinc-700 transition-colors hover:bg-zinc-100 hover:text-zinc-900 focus:bg-zinc-100 focus:text-zinc-900 focus:outline-none dark:text-zinc-300 dark:hover:bg-zinc-800 dark:hover:text-zinc-100 dark:focus:bg-zinc-800 dark:focus:text-zinc-100',
    ]) }}
>
    {{ $slot }}
</button>
