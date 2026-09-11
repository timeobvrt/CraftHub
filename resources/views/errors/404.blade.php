<x-layouts.app title="Page not found - CraftHub" :show-search="true">
    <div
        class="relative flex items-center justify-center overflow-hidden px-6 py-20"
    >
        <div
            class="pointer-events-none absolute left-1/2 top-1/2 h-125 w-125 -translate-x-1/2 -translate-y-1/2 rounded-full bg-crafthub/5 blur-3xl"
        ></div>

        <div class="relative mx-auto w-full max-w-2xl text-center">
            <div class="relative inline-flex">
                <span
                    class="select-none text-[8rem] font-black leading-none tracking-tighter text-crafthub/10 sm:text-[11rem]"
                >
                    404
                </span>
            </div>
            <div class="-mt-3">
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-crafthub">
                    Page not found
                </p>

                <h1
                    class="mt-4 text-3xl font-bold tracking-tight text-text sm:text-4xl"
                >
                    This page doesn't exist.
                </h1>

                <p
                    class="mx-auto mt-4 max-w-lg text-sm leading-7 text-muted sm:text-base"
                >The page you're looking for may have been moved, deleted, or never existed in the first place.</p>
            </div>

            <div
                class="mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row"
            >
                <a
                    href="{{ url('/') }}"
                    class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-crafthub px-5 text-sm font-semibold text-background transition hover:bg-crafthub-light"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        class="h-4 w-4"
                    >
                        <path d="m3 11 9-8 9 8" />
                        <path d="M5 10v10h14V10" />
                        <path d="M9 20v-6h6v6" />
                    </svg>

                    Back to home
                </a>

                <button
                    type="button"
                    onclick="history.back()"
                    class="inline-flex h-11 items-center justify-center gap-2 rounded-xl border border-white/10 bg-surface px-5 text-sm font-medium text-muted transition hover:border-crafthub/30 hover:bg-surface-light hover:text-text"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        class="h-4 w-4"
                    >
                        <path d="m15 18-6-6 6-6" />
                    </svg>

                    Go back
                </button>
            </div>
        </div>
    </div>
</x-layouts.app>
