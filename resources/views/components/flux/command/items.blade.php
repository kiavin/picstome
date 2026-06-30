@props([])

{{--
    Flux Pro Polyfill: <flux:command.items>
    Scrollable results list rendered below the command input.
    Overflow is clipped to the parent container's max-height.
--}}
<div
    {{ $attributes->merge([
        'class' => 'max-h-[60vh] overflow-y-auto overscroll-contain divide-y divide-zinc-100 dark:divide-zinc-800 p-1.5',
    ]) }}
    role="listbox"
    aria-label="{{ __('Search results') }}"
>
    {{ $slot }}
</div>
