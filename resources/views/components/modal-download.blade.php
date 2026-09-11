<div
    x-data="{
            open: false,
            loader: '',
            gameVersion: '',
            downloads: @js($downloads->all()),

            get matchingDownloads() {
                return this.downloads.filter((download) => {
                    const loaders = (download.loaders ?? [])
                        .map((loader) => String(loader).toLowerCase());

                    const versions = (download.game_versions ?? [])
                        .map((version) => String(version));

                    const loaderMatches =
                        !this.loader || loaders.includes(this.loader);

                    const versionMatches =
                        !this.gameVersion ||
                        versions.length === 0 ||
                        versions.includes(this.gameVersion);

                    return loaderMatches && versionMatches;
                });
            },

            reset() {
                this.loader = '';
                this.gameVersion = '';
            },

            close() {
                this.open = false;
                this.reset();
            },
        }"
    x-on:open-download-modal.window="open = true"
    x-on:keydown.escape.window="close()"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    role="dialog"
    aria-modal="true"
    aria-labelledby="download-modal-title"
>
    <div
        x-show="open"
        x-transition.opacity
        class="absolute inset-0 bg-black/70 backdrop-blur-sm"
        @click="close()"
    ></div>

    <div
        x-show="open"
        x-transition
        class="relative z-10 max-h-[90vh] w-full max-w-xl overflow-visible rounded-2xl border border-white/10 bg-surface p-6 shadow-2xl"
    >
        <div class="flex items-start justify-between gap-4">
            <div>
                <h2
                    id="download-modal-title"
                    class="text-xl font-semibold text-text"
                >
                    Download {{ $project->name() }}
                </h2>

                <p class="mt-1 text-sm text-muted">
                    Select your loader and Minecraft version.
                </p>
            </div>

            <button
                type="button"
                @click="close()"
                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg border border-white/10 text-muted transition hover:text-text"
                aria-label="Close"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    class="h-4 w-4"
                >
                    <path d="M18 6 6 18" />
                    <path d="m6 6 12 12" />
                </svg>
            </button>
        </div>

        <div class="mt-6 grid gap-4 sm:grid-cols-2">
            <x-select
                x-model="loader"
                name="download-loader"
                label="Loader"
                placeholder="All loaders"
                :options="$downloadLoaders->all()"
            />

            <x-select
                x-model="gameVersion"
                name="download-version"
                label="Minecraft version"
                placeholder="All versions"
                :options="$downloadGameVersions->all()"
            />
        </div>

        <div class="mt-6">
            <div class="mb-3 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-text">
                    Available downloads
                </h3>

                <span
                    class="text-xs text-muted"
                    x-text="`${matchingDownloads.length} result${matchingDownloads.length === 1 ? '' : 's'}`"
                ></span>
            </div>

            <div class="max-h-72 space-y-2 overflow-y-auto pr-1">
                <template
                    x-for="(download, index) in matchingDownloads"
                    :key="`${download.provider}:${download.url}:${index}`"
                >
                    <a
                        :href="download.url"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="group flex items-center justify-between gap-4 rounded-xl border border-white/10 bg-background p-4 transition hover:border-crafthub/30"
                    >
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <p
                                    class="truncate text-sm font-medium text-text"
                                    x-text="download.name ?? 'Download'"
                                ></p>

                                <span
                                    class="rounded-md bg-crafthub/10 px-2 py-1 text-[10px] font-medium text-crafthub"
                                    x-text="download.provider_label ?? download.provider"
                                ></span>

                                <span
                                    x-show="download.release_type"
                                    class="rounded-md border border-white/10 px-2 py-1 text-[10px] uppercase text-muted"
                                    x-text="download.release_type"
                                ></span>
                            </div>

                            <p
                                x-show="download.filename"
                                class="mt-1 truncate text-xs text-muted"
                                x-text="download.filename"
                            ></p>

                            <p
                                class="mt-1 truncate text-xs text-muted"
                                x-text="[
                                        ...(download.loaders ?? []),
                                        ...(download.game_versions ?? []).slice(0, 3),
                                    ].join(' • ')"
                            ></p>
                        </div>

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            class="h-5 w-5 shrink-0 text-muted transition group-hover:text-crafthub"
                        >
                            <path d="M12 3v12" />
                            <path d="m7 10 5 5 5-5" />
                            <path d="M5 21h14" />
                        </svg>
                    </a>
                </template>
            </div>

            <div
                x-show="matchingDownloads.length === 0"
                class="rounded-xl border border-white/10 bg-background px-5 py-10 text-center"
            >
                <p class="text-sm font-medium text-text">
                    No compatible download
                </p>

                <p class="mt-1 text-sm text-muted">
                    Try another loader or Minecraft version.
                </p>

                <button
                    type="button"
                    @click="reset()"
                    class="mt-4 text-sm font-medium text-crafthub hover:text-crafthub-light"
                >
                    Clear selection
                </button>
            </div>
        </div>
    </div>
</div>
