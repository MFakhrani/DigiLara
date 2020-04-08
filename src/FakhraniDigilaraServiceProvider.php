<?php

namespace Fakhrani\Digilara;

use Fakhrani\Digilara\Commands\MigrateCheckCommand;
use Fakhrani\Digilara\Commands\NewVersion;
use Fakhrani\Digilara\Commands\NewVersionModule;
use Fakhrani\Digilara\Commands\VendorCleanUpCommand;
use Illuminate\Support\ServiceProvider;

class FakhraniDigilaraServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/Config/fakhrani_digilara.php','fakhrani_digilara'
        );
        $this->commands([
            VendorCleanUpCommand::class,
            NewVersion::class,
            NewVersionModule::class,
            MigrateCheckCommand::class
        ]);
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function boot()
    {
        $this->publishFiles();

        include __DIR__ . '/routes.php';
    }

    public function publishFiles()
    {
        $this->publishes([
            __DIR__ . '/Config/fakhrani_digilara.php' => config_path('fakhrani_digilara.php'),
        ]);

        $this->publishes([
            __DIR__ . '/Migrations/' => database_path('migrations')
        ], 'migrations');

        $this->publishes([
            __DIR__ . '/Views' => resource_path('views/vendor/fakhrani-digilara'),
        ]);

    }
}
