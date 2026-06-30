@props([
    'name' => null,
])

{{--
    Flux Pro Polyfill: <flux:tab>
    An individual tab button inside `flux:tabs`.

    The parent `flux:tab.group` sets `x-data="{ tab: '...' }"` directly on
    itself. This button sets `tab` in that scope and checks against it to
    apply the active styling — no additional JS needed.

    Props:
      name — must match the corresponding `flux:tab.panel` name
--}}
<button
    type="button"
    role="tab"
    :aria-selected="tab === {{ Js::from($name) }}"
    x-on:click="tab = {{ Js::from($name) }}"
    :class="{
        'border-b-2 border-zinc-900 text-zinc-900 dark:border-zinc-100 dark:text-zinc-100': tab === {{ Js::from($name) }},
        'border-b-2 border-transparent text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200': tab !== {{ Js::from($name) }}
    }"
    {{ $attributes->merge([
        'class' => '-mb-px whitespace-nowrap px-4 py-2.5 text-sm font-medium transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-zinc-500',
    ]) }}
>
    {{ $slot }}
</button>
