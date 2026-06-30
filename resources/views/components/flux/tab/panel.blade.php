@props([
    'name' => null,
])

{{--
    Flux Pro Polyfill: <flux:tab.panel>
    Content panel shown when the matching tab is active.

    Works in tandem with the `tab` variable from the parent Alpine scope
    set by `flux:tab.group` (`x-data="{ tab: '...' }"`).

    Props:
      name — must match the `flux:tab` with the same name
--}}
<div
    role="tabpanel"
    x-show="tab === {{ Js::from($name) }}"
    x-cloak
    {{ $attributes->merge([
        'class' => 'w-full',
    ]) }}
>
    {{ $slot }}
</div>
