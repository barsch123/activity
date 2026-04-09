<?php

namespace Gottvergessen\Activity;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Gottvergessen\Activity\Commands\ActivityInstallCommand;
use Gottvergessen\Activity\Commands\ActivityPruneCommand;
use Gottvergessen\Activity\Commands\ActivityInstallDashboardCommand;

class ActivityServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('activity')
            ->hasConfigFile()
            ->hasMigration('create_logger_table')
            ->hasCommands([
                ActivityInstallCommand::class,
                ActivityPruneCommand::class,
                ActivityInstallDashboardCommand::class,
            ]);
    }

    public function packageBooted(): void
    {
        // Publish Blade dashboard
        $this->publishes([
            __DIR__ . '/../stubs/dashboard/blade' => resource_path('views/activity'),
        ], 'activity-dashboard-blade');

        // Publish React dashboard
        $this->publishes([
            __DIR__ . '/../stubs/dashboard/react' => resource_path('js/Pages/Activity'),
        ], 'activity-dashboard-react');

        // Publish Vue dashboard
        $this->publishes([
            __DIR__ . '/../stubs/dashboard/vue' => resource_path('js/Pages/Activity'),
        ], 'activity-dashboard-vue');

        // Publish controller
        $this->publishes([
            __DIR__ . '/../stubs/controller' => app_path('Http/Controllers'),
        ], 'activity-dashboard-controller');

        // Publish routes
        $this->publishes([
            __DIR__ . '/../stubs/routes/activity-dashboard.php' => base_path('routes/activity-dashboard.php'),
        ], 'activity-dashboard-routes');
    }
}
