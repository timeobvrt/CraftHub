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
        class="absolute inset-0 bg-black/70"
        @click="close()"
    ></div>

    <div
        x-show="open"
        x-transition
        class="relative z-10 max-h-[90vh] w-full max-w-2xl overflow-hidden border border-white/10 bg-surface"
    >
        <div class="flex items-start justify-between gap-6 border-b border-white/10 px-5 py-4">
            <div class="min-w-0">
                <div class="flex items-center gap-2">
                    <i class="fa-pixel fa-regular fa-arrow-down-to-bracket fa-lg text-crafthub"></i>

                    <h1
                        id="download-modal-title"
                        class="text-lg font-pixel text-text"
                    >
                        Download
                    </h1>
                </div>
            </div>

            <button
                type="button"
                @click="close()"
                class="flex h-7 w-7 shrink-0 cursor-pointer items-center justify-center border border-white/10 text-muted transition-colors hover:border-white/20 hover:text-text"
                aria-label="Close"
            >
                <i class="fa-pixel fa-regular fa-xmark h-3.5 w-3.5"></i>
            </button>
        </div>

        <div class="border-b border-white/10 px-5 py-4">
            <div class="grid gap-4 sm:grid-cols-2">
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
        </div>

        <div class="p-5">
            <div class="mb-3 flex items-center justify-between">
                <h3 class="text-xs font-pixel text-text">
                    Available downloads
                </h3>
            </div>

            <div class="max-h-[55vh] space-y-2 overflow-y-auto">
                <template
                    x-for="(download, index) in matchingDownloads"
                    :key="`${download.provider}:${download.url}:${index}`"
                >
                    <a
                        :href="download.url"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="group flex items-center gap-4 border border-white/10 bg-background p-3 transition-colors hover:border-crafthub/30"
                    >
                        <div class="min-w-0 flex-1">
                            <div class="flex min-w-0 flex-wrap items-center gap-2">
                                <p
                                    class="truncate text-sm font-medium text-text group-hover:text-crafthub"
                                    x-text="download.name ?? 'Download'"
                                ></p>

                                <span
                                    x-show="download.release_type"
                                    class="text-[9px] uppercase text-muted"
                                    x-text="download.release_type"
                                ></span>
                            </div>

                            <p
                                x-show="download.filename"
                                class="mt-1 truncate text-[10px] text-muted"
                                x-text="download.filename"
                            ></p>

                            <p
                                class="mt-1 truncate text-[10px] text-muted/70"
                                x-text="[
                                ...(download.loaders ?? []),
                                ...(download.game_versions ?? []).slice(0, 3),
                            ].join(' • ')"
                            ></p>
                        </div>

                        <i
                            class="fa-pixel fa-regular fa-arrow-down-to-bracket h-4 w-4 shrink-0 text-muted/40 transition-colors group-hover:text-crafthub"
                        ></i>
                    </a>
                </template>
            </div>

            <div
                x-show="matchingDownloads.length === 0"
                class="border border-white/10 bg-background px-5 py-10 text-center"
            >
                <i class="fa-pixel fa-regular fa-box-open h-5 w-5 text-muted/50"></i>

                <p class="mt-3 text-sm font-medium text-text">
                    No compatible download
                </p>

                <p class="mt-1 text-xs text-muted">
                    Try another loader or Minecraft version.
                </p>

                <button
                    type="button"
                    @click="reset()"
                    class="mt-4 cursor-pointer text-xs font-medium text-crafthub transition-colors hover:text-crafthub-light"
                >
                    Clear selection
                </button>
            </div>
        </div>
    </div>
</div>
