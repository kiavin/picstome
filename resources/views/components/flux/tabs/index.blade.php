@props([
    'size'            => 'base',
    'scrollable'      => false,
    'scrollable:fade' => false,
])

{{--
    Flux Pro Polyfill: <flux:tabs>
    Renders a horizontal tab navigation bar (role="tablist").
    Communicates with `flux:tab` siblings via a shared Alpine parent scope.

    Props:
      size           — 'sm' | 'base' | 'lg'  (adjusts text/padding)
      scrollable     — enables horizontal overflow scrolling on small screens
      scrollable:fade — adds a fade gradient at the right edge (cosmetic)
--}}
@php
    $sizeClasses = match($size) {
        'sm'  => 'text-xs',
        'lg'  => 'text-base',
        default => 'text-sm',
    };
@endphp

<div
    {{ $attributes->merge([
        'class' => 'relative',
    ]) }}
>
    <nav
        role="tablist"
        class="
            flex border-b border-zinc-200 dark:border-zinc-700
            {{ $scrollable ? 'overflow-x-auto scrollbar-hide' : '' }}
            {{ $sizeClasses }}
        "
        aria-label="{{ __('Tabs') }}"
    >
        {{ $slot }}
    </nav>

    @if ($scrollable)
        {{-- Fade-out gradient on the right edge --}}
        <div class="pointer-events-none absolute right-0 top-0 h-full w-8 bg-gradient-to-l from-white dark:from-zinc-900" aria-hidden="true"></div>
    @endif
</div>
