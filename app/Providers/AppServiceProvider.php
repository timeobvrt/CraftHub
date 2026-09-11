<?php

namespace App\Providers;

use App\Contracts\ProjectProvider;
use App\Services\ProjectProviderRegistry;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->tag(
            [
                ModrinthProjectProvider::class,
                SpigotProjectProvider::class,
            ],
            ProjectProvider::class,
        );

        $this->app->singleton(
            ProjectProviderRegistry::class,
            fn(
                $app
            ) => new ProjectProviderRegistry(
                $app->tagged(ProjectProvider::class),
            )
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
