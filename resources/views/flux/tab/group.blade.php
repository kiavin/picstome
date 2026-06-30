@props([])

{{--
    Flux Pro Polyfill: <flux:tab.group>
    Container that establishes the Alpine.js tab context.
    The parent can provide `x-data="{ tab: 'rater' }"` directly on this
    element; we simply forward all attributes so that pattern is preserved.

    Child `flux:tabs` + `flux:tab` + `flux:tab.panel` components communicate
    via Alpine's `$dispatch` / `x-on` on a shared `x-data` scope.

    If no `x-data` is provided by the parent, we initialize a minimal default.
--}}
<div
    {{ $attributes->merge([
        'class' => 'w-full',
    ]) }}
>
    {{ $slot }}
</div>
