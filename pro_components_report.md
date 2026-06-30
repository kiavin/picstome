# Flux Pro Components — Audit Report

Generated: 2026-06-30

## Summary

| Metric | Count |
|---|---|
| Total Flux components used | 83 |
| Available in free `livewire/flux` | 73 |
| **Missing (Pro-only)** | **10** |

All 10 missing Pro components have been polyfilled under `resources/views/flux/`.

---

## Phase 1 — Component Discovery

**Scan command used:**
```bash
grep -RhoE '<flux:[A-Za-z0-9._-]+' resources/views \
  | sed 's/<flux://' \
  | sort -u \
  > used_components.txt
```

Full list of unique components found: see `used_components.txt`.

---

## Phase 2 — Gap Analysis: Missing (Pro-Only) Components

The following components were referenced in the application but absent from the `vendor/livewire/flux` (free) package:

---

### `flux:command`

| Attribute | Detail |
|---|---|
| **Nested** | No (root component) |
| **Usage count** | 1 |
| **File(s)** | `resources/views/components/⚡search.blade.php` |
| **Line(s)** | 118, 184 |
| **Example markup** | `<flux:command class="inline-flex max-h-[76vh] flex-col border-none shadow-lg">` |
| **Inferred purpose** | Root container for the command palette / search overlay |
| **Implementation** | Styled `<div>` with rounded corners, border, shadow; slots inner content |

---

### `flux:command.input`

| Attribute | Detail |
|---|---|
| **Nested** | Yes (`command.input`) |
| **Usage count** | 1 |
| **File(s)** | `resources/views/components/⚡search.blade.php` |
| **Line(s)** | 119 |
| **Example markup** | `<flux:command.input wire:model.live="search" placeholder="{{ __('Search...') }}" autofocus closable />` |
| **Inferred purpose** | Search input with magnifier icon, `wire:model` forwarding, and optional close (×) button |
| **Props** | `placeholder`, `autofocus`, `closable` |
| **Implementation** | `<input type="text">` with search SVG icon prefix; closable button clears `$wire.search` via Alpine |

---

### `flux:command.items`

| Attribute | Detail |
|---|---|
| **Nested** | Yes (`command.items`) |
| **Usage count** | 1 |
| **File(s)** | `resources/views/components/⚡search.blade.php` |
| **Line(s)** | 122, 182 |
| **Example markup** | `<flux:command.items>...</flux:command.items>` |
| **Inferred purpose** | Scrollable `role="listbox"` container that holds result items |
| **Implementation** | `<div>` with `overflow-y-auto`, `max-h-[60vh]`, padding, and result dividers |

---

### `flux:command.item`

| Attribute | Detail |
|---|---|
| **Nested** | Yes (`command.item`) |
| **Usage count** | 6 |
| **File(s)** | `resources/views/components/⚡search.blade.php` |
| **Line(s)** | 128, 144, 155, 166, 177 |
| **Example markup** | `<flux:command.item wire:click="viewCustomer('{{ $customer->id }}')">` |
| **Inferred purpose** | Individual result row in the command palette — clickable, keyboard-navigable |
| **Props** | Forwards all attributes (`wire:click`, etc.) |
| **Implementation** | `<button role="option">` with hover/focus Zinc styling |

---

### `flux:tabs`

| Attribute | Detail |
|---|---|
| **Nested** | No (sibling of `flux:tab`, child of `flux:tab.group`) |
| **Usage count** | 1 |
| **File(s)** | `resources/views/pages/galleries/⚡show.blade.php` |
| **Line(s)** | 760 |
| **Example markup** | `<flux:tabs size="sm" class="w-full" scrollable scrollable:fade>` |
| **Inferred purpose** | Horizontal `<nav role="tablist">` bar containing tab buttons |
| **Props** | `size` (`sm`/`base`/`lg`), `scrollable`, `scrollable:fade` |
| **Implementation** | `<nav>` with bottom border, horizontal overflow scrolling, optional fade gradient |

---

### `flux:tab`

| Attribute | Detail |
|---|---|
| **Nested** | Yes (child of `flux:tabs`) |
| **Usage count** | 5 |
| **File(s)** | `resources/views/pages/galleries/⚡show.blade.php` |
| **Line(s)** | 761–765 |
| **Example markup** | `<flux:tab name="rater">{{ __('App') }}</flux:tab>` |
| **Inferred purpose** | Individual tab button that sets the active tab in the parent Alpine scope |
| **Props** | `name` (must match the corresponding `flux:tab.panel`) |
| **Implementation** | `<button role="tab">` that reads/writes `tab` from parent `x-data` scope; active-state border underline via `:class` |

---

### `flux:tab.group`

| Attribute | Detail |
|---|---|
| **Nested** | Yes (`tab.group`) |
| **Usage count** | 1 |
| **File(s)** | `resources/views/pages/galleries/⚡show.blade.php` |
| **Line(s)** | 759, 873 |
| **Example markup** | `<flux:tab.group x-data="{ tab: 'rater' }">` |
| **Inferred purpose** | Container that establishes the Alpine.js `tab` data context used by child tabs and panels |
| **Implementation** | Lightweight `<div>` that forwards all attributes — the `x-data` applied by the parent is preserved and shared with children |

---

### `flux:tab.panel`

| Attribute | Detail |
|---|---|
| **Nested** | Yes (`tab.panel`) |
| **Usage count** | 5 |
| **File(s)** | `resources/views/pages/galleries/⚡show.blade.php` |
| **Line(s)** | 768, 795, 816, 837, 858 |
| **Example markup** | `<flux:tab.panel name="rater" class="pt-4!">` |
| **Inferred purpose** | Content panel shown when the matching tab is active |
| **Props** | `name` (must match the `flux:tab` `name`) |
| **Implementation** | `<div role="tabpanel">` with `x-show="tab === '{name}'"` using `x-cloak` |

---

### `flux:date-picker`

| Attribute | Detail |
|---|---|
| **Nested** | No |
| **Usage count** | 1 |
| **File(s)** | `resources/views/pages/⚡payments.blade.php` |
| **Line(s)** | 256 |
| **Example markup** | `<flux:date-picker wire:model="linkForm.booking_date" :label="__('Date')" />` |
| **Inferred purpose** | Date selection field with label; forwards `wire:model` to native `<input type="date">` |
| **Props** | `label`, `name`, `placeholder` |
| **Implementation** | `<input type="date">` with Flux-matching border/shadow/rounded styling; calendar SVG icon prefix; validation error display |

---

### `flux:time-picker`

| Attribute | Detail |
|---|---|
| **Nested** | No |
| **Usage count** | 2 |
| **File(s)** | `resources/views/pages/⚡payments.blade.php` |
| **Line(s)** | 258–259 |
| **Example markup** | `<flux:time-picker wire:model="linkForm.booking_start_time" :label="__('Start Time')" />` |
| **Inferred purpose** | Time selection field with label; forwards `wire:model` to native `<input type="time">` |
| **Props** | `label`, `name` |
| **Implementation** | `<input type="time">` with Flux-matching border/shadow/rounded styling; clock SVG icon prefix; validation error display |

---

## Phase 3 — Polyfill Locations

All polyfills live under `resources/views/flux/` — the directory checked first by the Flux service provider:

```
resources/views/flux/
├── command/
│   ├── index.blade.php   → <flux:command>
│   ├── input.blade.php   → <flux:command.input>
│   ├── item.blade.php    → <flux:command.item>
│   └── items.blade.php   → <flux:command.items>
├── date-picker/
│   └── index.blade.php   → <flux:date-picker>
├── tab/
│   ├── group.blade.php   → <flux:tab.group>
│   ├── index.blade.php   → <flux:tab>
│   └── panel.blade.php   → <flux:tab.panel>
├── tabs/
│   └── index.blade.php   → <flux:tabs>
└── time-picker/
    └── index.blade.php   → <flux:time-picker>
```

**Resolution mechanism:** The Flux service provider registers `resources/views/flux/` as the first path in the `flux` Blade view namespace. When a `<flux:X>` tag is compiled, Laravel's `ComponentTagCompiler` walks through the registered paths in order — our local override directory is checked before the vendor package.

---

## Phase 4 — Validation Results

All 10 polyfills pass `Blade::render()` without exceptions:

```
✓ command
✓ command.input
✓ command.items
✓ command.item
✓ tabs + tab
✓ tab.group + tab.panel
✓ date-picker
✓ date-picker with wire:model
✓ time-picker
✓ time-picker with wire:model
```

---

## Component API Compatibility Matrix

| Component | Attribute Forwarding | Props | Slots | wire:model | Alpine | Accessibility |
|---|:---:|:---:|:---:|:---:|:---:|:---:|
| `flux:command` | ✅ | — | ✅ | — | — | — |
| `flux:command.input` | ✅ | `placeholder`, `autofocus`, `closable` | — | ✅ | ✅ (clear button) | `aria-label` |
| `flux:command.items` | ✅ | — | ✅ | — | — | `role="listbox"` |
| `flux:command.item` | ✅ | — | ✅ | — | — | `role="option"` |
| `flux:tabs` | ✅ | `size`, `scrollable`, `scrollable:fade` | ✅ | — | — | `role="tablist"` |
| `flux:tab` | ✅ | `name` | ✅ | — | ✅ (active state) | `role="tab"`, `aria-selected` |
| `flux:tab.group` | ✅ | — | ✅ | — | ✅ (context) | — |
| `flux:tab.panel` | ✅ | `name` | ✅ | — | ✅ (`x-show`) | `role="tabpanel"` |
| `flux:date-picker` | ✅ | `label`, `name`, `placeholder` | — | ✅ | — | Icon, label association |
| `flux:time-picker` | ✅ | `label`, `name` | — | ✅ | — | Icon, label association |
