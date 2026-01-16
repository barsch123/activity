<?php

namespace Gottvergessen\Logger;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Spatie\LaravelPackageTools\Package;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Console\Events\CommandFinished;
use Gottvergessen\Logger\Support\ActivityContext;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\LaravelPackageTools\Commands\InstallCommand;

class LoggerServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('logger')
            ->hasConfigFile('logger')
            ->hasMigration('create_logger_table')
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToRunMigrations()
                    ->askToStarRepoOnGitHub('gottvergessen/logger');
            });
    }

   
}
