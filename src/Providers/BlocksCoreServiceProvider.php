<?php

declare(strict_types=1);

namespace Reker7\MoonShineBlocksCore\Providers;

use Illuminate\Support\ServiceProvider;

final class BlocksCoreServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

            $this->publishes([
                __DIR__ . '/../../database/migrations' => database_path('migrations'),
            ], 'moonshine-blocks-core-migrations');
        }
    }
}
