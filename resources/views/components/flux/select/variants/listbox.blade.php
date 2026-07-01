@props([
    'name'        => null,
    'placeholder' => null,
    'invalid'     => null,
    'size'        => null,
    'searchable'  => false,
    'clearable'   => false,
    'multiple'    => false,
])

{{--
    Flux Pro Polyfill: <flux:select variant="listbox">
    A custom, Alpine.js-powered listbox that replaces the native <select> element.
    Supports searchable filtering, placeholder, wire:model, and keyboard navigation.

    API:
      - wire:model / x-model  (forwarded to the hidden real <select>)
      - variant="listbox"     (this file)
      - searchable            (adds a search input inside the dropdown)
      - clearable             (adds a × button to clear the selection)
      - placeholder           (shown when no value selected)
      - size                  (sm | base)
      - name / id             (forwarded to the hidden <select>)

    Child flux:select.option elements are rendered inside a hidden native <select>
    and mirrored into the custom listbox. The hidden <select> carries wire:model
    so Livewire receives the value; Alpine syncs the display.
--}}
@php
    $inputName = $name ?? $attributes->whereStartsWith('wire:model')->first()
        ?? $attributes->whereStartsWith('x-model')->first();

    $sizeClasses = match ($size) {
        'sm'  => 'h-8 text-sm rounded-md',
        'xs'  => 'h-6 text-xs rounded-md',
        default => 'h-10 text-base sm:text-sm rounded-lg',
    };
@endphp

<div
    x-data="{
        open: false,
        search: '',
        selectedValue: '',
        selectedLabel: '',

        init() {
            // Grab initial value from the hidden native select
            const sel = this.$refs.nativeSelect;
            if (sel && sel.value) {
                this.selectedValue = sel.value;
                this.selectedLabel = sel.options[sel.selectedIndex]?.text ?? '';
            }

            // Watch for Livewire / Alpine model updates on the hidden select
            this.$watch(() => this.$refs.nativeSelect?.value, (val) => {
                if (val === undefined) return;
                this.selectedValue = val;
                const sel = this.$refs.nativeSelect;
                const opt = Array.from(sel.options).find(o => o.value === val);
                this.selectedLabel = opt ? opt.text : '';
            });
        },

        selectOption(value, label) {
            this.selectedValue = value;
            this.selectedLabel = label;

            // Keep the hidden native select in sync
            const sel = this.$refs.nativeSelect;
            sel.value = value;
            sel.dispatchEvent(new Event('change', { bubbles: true }));
            sel.dispatchEvent(new Event('input',  { bubbles: true }));

            this.open = false;
            this.search = '';
        },

        clear() {
            this.selectOption('', '');
        },

        get filteredOptions() {
            const q = this.search.toLowerCase().trim();
            return Array.from(this.$refs.nativeSelect?.options ?? []).filter(o => {
                return !q || o.text.toLowerCase().includes(q);
            });
        },
    }"
    x-on:keydown.escape="open = false"
    x-on:click.outside="open = false"
    class="relative w-full"
>
    {{-- Trigger button --}}
    <button
        type="button"
        x-on:click="open = !open"
        :aria-expanded="open"
        aria-haspopup="listbox"
        class="
            flex w-full items-center justify-between gap-2 px-3
            border border-zinc-200 border-b-zinc-300/80 bg-white shadow-xs
            text-left transition
            focus:outline-none focus-visible:ring-2 focus-visible:ring-zinc-400
            dark:border-white/10 dark:bg-white/10 dark:text-zinc-300
            {{ $sizeClasses }}
        "
    >
        <span
            class="block flex-1 truncate"
            :class="selectedValue ? 'text-zinc-700 dark:text-zinc-200' : 'text-zinc-400 dark:text-zinc-500'"
            x-text="selectedLabel || {{ Js::from($placeholder ?? __('Select...')) }}"
        ></span>

        <span class="flex shrink-0 items-center gap-1">
            @if ($clearable)
                <span
                    x-show="selectedValue !== ''"
                    x-on:click.stop="clear()"
                    class="cursor-pointer rounded p-0.5 text-zinc-400 hover:text-zinc-600 dark:text-zinc-500 dark:hover:text-zinc-300"
                    aria-label="{{ __('Clear') }}"
                >
                    <svg class="size-3.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z"/></svg>
                </span>
            @endif

            {{-- Chevron --}}
            <svg class="size-4 text-zinc-400 transition-transform duration-150 dark:text-zinc-500" :class="open ? 'rotate-180' : ''" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 011.06 0L10 11.94l3.72-3.72a.75.75 0 111.06 1.06l-4.25 4.25a.75.75 0 01-1.06 0L5.22 9.28a.75.75 0 010-1.06z" clip-rule="evenodd"/></svg>
        </span>
    </button>

    {{-- Dropdown panel --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        x-cloak
        class="absolute z-50 mt-1 w-full min-w-[12rem] overflow-hidden rounded-lg border border-zinc-200 bg-white shadow-lg dark:border-zinc-700 dark:bg-zinc-900"
        role="listbox"
    >
        @if ($searchable)
            {{-- Search input --}}
            <div class="border-b border-zinc-100 p-1.5 dark:border-zinc-800">
                <div class="relative flex items-center">
                    <svg class="pointer-events-none absolute left-2.5 size-3.5 text-zinc-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd"/></svg>
                    <input
                        type="text"
                        x-model="search"
                        x-ref="searchInput"
                        x-on:keydown.escape.stop="open = false"
                        placeholder="{{ __('Search...') }}"
                        class="w-full rounded-md bg-transparent py-1.5 pl-8 pr-3 text-sm text-zinc-700 placeholder-zinc-400 focus:outline-none dark:text-zinc-200 dark:placeholder-zinc-500"
                        autocomplete="off"
                    />
                </div>
            </div>
        @endif

        {{-- Options list --}}
        <ul
            class="max-h-56 overflow-y-auto py-1"
            role="listbox"
            x-on:keydown.arrow-down.prevent="$focus.wrap().next()"
            x-on:keydown.arrow-up.prevent="$focus.wrap().previous()"
        >
            <template x-for="opt in filteredOptions" :key="opt.value">
                <li
                    role="option"
                    :aria-selected="opt.value === selectedValue"
                    x-on:click="selectOption(opt.value, opt.text)"
                    class="flex cursor-pointer items-center justify-between gap-2 px-3 py-2 text-sm"
                    :class="{
                        'bg-zinc-100 text-zinc-900 dark:bg-zinc-800 dark:text-zinc-100': opt.value === selectedValue,
                        'text-zinc-700 hover:bg-zinc-50 dark:text-zinc-300 dark:hover:bg-zinc-800': opt.value !== selectedValue
                    }"
                >
                    <span x-text="opt.text"></span>
                    <svg x-show="opt.value === selectedValue" class="size-4 shrink-0 text-zinc-600 dark:text-zinc-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/></svg>
                </li>
            </template>

            {{-- Empty state --}}
            <li
                x-show="filteredOptions.length === 0"
                class="px-3 py-2 text-sm text-zinc-500 dark:text-zinc-400"
                aria-disabled="true"
            >
                {{ __('No results found.') }}
            </li>
        </ul>
    </div>

    {{--
        Hidden native <select> — carries wire:model so Livewire syncs the value.
        Alpine reads/writes this element's .value to stay in sync.
    --}}
    <select
        x-ref="nativeSelect"
        class="sr-only"
        tabindex="-1"
        aria-hidden="true"
        {{ $attributes->only(['wire:model', 'wire:model.live', 'wire:model.defer', 'x-model', 'name', 'id', 'multiple']) }}
        @if ($inputName) name="{{ $inputName }}" @endif
        data-flux-control
    >
        @if ($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif
        {{ $slot }}
    </select>
</div>
