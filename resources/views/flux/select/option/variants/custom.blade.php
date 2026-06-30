@props([
    'value' => null,
])

{{--
    Flux Pro Polyfill: <flux:select.option> for variant="listbox" (custom variant)
    Renders a native <option> element inside the hidden <select> used by our
    listbox polyfill. The custom Alpine listbox reads the native select's options
    to build its display list, so a plain <option> is exactly what we need.
--}}
<option
    {{ $attributes }}
    @if (isset($value)) value="{{ $value }}" @endif
    @if (isset($value)) wire:key="{{ $value }}" @endif
>{{ $slot }}</option>
