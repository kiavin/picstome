@props([
    'label'       => null,
    'placeholder' => null,
    'name'        => null,
])

{{--
    Flux Pro Polyfill: <flux:date-picker>
    Wraps a native <input type="date"> in the same field/label layout
    as the rest of the Flux form components.

    Supports:
      - wire:model / wire:model.live  (forwarded to the <input>)
      - :label                        (renders a <flux:label> above the input)
      - :placeholder                  (native placeholder)
      - All other attributes are forwarded to the <input> element.
--}}
@php
    $inputName = $name ?? $attributes->whereStartsWith('wire:model')->first();
    $hasError  = $inputName && isset($errors) && $errors->has($inputName);
    $idAttr    = $inputName ? 'id="' . e($inputName) . '" name="' . e($inputName) . '"' : '';
@endphp

<div class="grid w-full gap-1.5">
    @if ($label)
        <flux:label>{!! $label !!}</flux:label>
    @endif

    <div class="group/input relative block w-full" data-flux-input>
        {{-- Calendar icon --}}
        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3" aria-hidden="true">
            <svg class="size-4 text-zinc-400/75 dark:text-white/60" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
            </svg>
        </div>

        <input
            type="date"
            {!! $idAttr !!}
            {{ $attributes->except(['label', 'name', 'placeholder'])->merge([
                'class'       => 'block w-full rounded-lg border border-b-zinc-300/80 bg-white py-2 pl-10 pr-3 text-sm text-zinc-700 shadow-xs placeholder-zinc-400 transition focus:outline-none focus:ring-1 dark:bg-white/10 dark:text-zinc-300 dark:placeholder-zinc-400 '
                    . ($hasError ? 'border-red-500 focus:border-red-500 focus:ring-red-300 dark:border-red-500' : 'border-zinc-200 focus:border-zinc-400 focus:ring-zinc-300 dark:border-white/10 dark:focus:border-white/20'),
                'placeholder' => $placeholder ?? '',
                'aria-invalid' => $hasError ? 'true' : false,
            ]) }}
            data-flux-control
        />
    </div>

    @if ($hasError)
        <p class="text-sm text-red-600 dark:text-red-400">{{ $errors->first($inputName) }}</p>
    @endif
</div>
