@props ([
    'name',
    'label' => null,
    'options' => [],
    'value' => null,
    'placeholder' => null,
])

@php
    $selectedValue = (string) old($name, request($name, $value ?? ''));

    $selectedLabel = $placeholder;

    if ($selectedValue !== '' && array_key_exists($selectedValue, $options)) {
        $selectedLabel = $options[$selectedValue];
    }
@endphp

<div
    x-data="{
        open: false,
        value: @js($selectedValue),
        label: @js($selectedLabel),

        select(value, label) {
            this.value = value;
            this.label = label;
            this.open = false;
        }
    }"
    x-modelable="value"
    {{ $attributes->class(['relative'])  }}
    @keydown.escape.window="open = false"
>
    @if ($label)
        <label
            for="{{ $name }}"
            class="mb-2 block text-sm font-medium text-text"
        >
            {{ $label }}
        </label>
    @endif

    <input type="hidden" name="{{ $name }}" :value="value" />

    <button
        type="button"
        id="{{ $name }}"
        @click="open = !open"
        :aria-expanded="open"
        class="group flex w-full items-center justify-between gap-3 rounded-xl border border-white/10 bg-surface px-3.5 py-2.5 text-left text-sm text-text outline-none transition-all duration-150 hover:border-white/20 hover:bg-surface-light/40 focus:border-crafthub/50 focus:ring-4 focus:ring-crafthub/5"
        :class="{
            'border-crafthub/50 ring-4 ring-crafthub/5': open,
        }"
    >
        <span
            class="min-w-0 flex-1 truncate"
            x-text="label || @js($placeholder ?? 'Select an option')"
        ></span>

        <svg
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
            class="h-4 w-4 shrink-0 text-muted transition-transform duration-200 group-hover:text-text"
            :class="{ 'rotate-180': open }"
        >
            <path d="m6 9 6 6 6-6" />
        </svg>
    </button>

    <div
        x-cloak
        x-show="open"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 translate-y-1 scale-[0.98]"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-1 scale-[0.98]"
        @click.outside="open = false"
        class="absolute z-50 mt-2 max-h-72 w-full overflow-y-auto rounded-xl border border-white/10 bg-surface p-1.5 shadow-2xl shadow-black/30"
    >
        @if ($placeholder !== null)
            <button
                type="button"
                @click="select('', @js($placeholder))"
                class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-left text-sm transition hover:bg-white/5"
                :class="value === ''
                    ? 'bg-crafthub/10 text-crafthub'
                    : 'text-muted'"
            >
                <span>{{ $placeholder }}</span>

                <svg
                    x-show="value === ''"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    class="h-4 w-4"
                >
                    <path d="m20 6-11 11-5-5" />
                </svg>
            </button>
        @endif

        @foreach ($options as $optionValue => $optionLabel)
            <button
                type="button"
                @click="select(
                    @js((string) $optionValue),
                    @js($optionLabel)
                )"
                class="flex w-full items-center justify-between gap-3 rounded-lg px-3 py-2 text-left text-sm transition hover:bg-white/5"
                :class="
                    value === @js((string) $optionValue)
                        ? 'bg-crafthub/10 text-crafthub'
                        : 'text-text'
                "
            >
                <span class="truncate"> {{ $optionLabel }} </span>

                <svg
                    x-show="value === @js((string) $optionValue)"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    class="h-4 w-4 shrink-0"
                >
                    <path d="m20 6-11 11-5-5" />
                </svg>
            </button>
        @endforeach
    </div>

    @error ($name)
    <p class="mt-2 text-xs text-red-400">{{ $message }}</p>
    @enderror
</div>
