<?php

declare(strict_types=1);

namespace Reker7\MoonShineBlocksCore\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\TestCase as Orchestra;
use Reker7\MoonShineBlocksCore\Providers\BlocksCoreServiceProvider;

abstract class TestCase extends Orchestra
{
    use RefreshDatabase;

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
        $app['config']->set('app.debug', true);
    }

    protected function getPackageProviders($app): array
    {
        return [
            BlocksCoreServiceProvider::class,
        ];
    }
}
