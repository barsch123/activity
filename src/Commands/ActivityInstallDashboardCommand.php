<?php

namespace Gottvergessen\Activity\Commands;

use Illuminate\Console\Command;

class ActivityInstallDashboardCommand extends Command
{
    protected $signature = 'activity:install-dashboard {framework? : blade, react, or vue} {--force : Overwrite existing published files}';

    protected $description = 'Install the Activity dashboard for your preferred framework';

    public function handle(): int
    {
        $this->components->info('Activity Dashboard Installer ✨');
        $this->newLine();

        $framework = $this->argument('framework') 
            ?? $this->choice(
                'Which framework would you like to use for the dashboard?',
                ['blade', 'react', 'vue'],
                0
            );

        if (! in_array($framework, ['blade', 'react', 'vue'])) {
            $this->components->error("Invalid framework: {$framework}. Choose blade, react, or vue.");
            return self::FAILURE;
        }

        $this->components->info("Installing {$framework} dashboard...");
        $this->newLine();

        // Publish dashboard views/components
        $this->publishDashboard($framework);

        // Publish controller
        $this->publishController();

        // Publish routes
        $this->publishRoutes();

        // Register dashboard routes in routes/web.php
        $this->registerRouteFile();

        $this->newLine();
        $this->components->success('Dashboard installed successfully! ✅');
        $this->newLine();

        $this->line('Next steps:');
        $this->line('  • Review routes/web.php for the new /app/logs route');
        
        if ($framework === 'blade') {
            $this->line('  • Check resources/views/activity/dashboard.blade.php');
        } elseif ($framework === 'react') {
            $this->line('  • Check resources/js/Pages/Activity/Dashboard.jsx');
        } else {
            $this->line('  • Check resources/js/Pages/Activity/Dashboard.vue');
        }

        return self::SUCCESS;
    }

    protected function publishDashboard(string $framework): void
    {
        $this->components->task(
            "Publishing {$framework} dashboard files",
            fn() => $this->callSilent('vendor:publish', [
                '--tag' => "activity-dashboard-{$framework}",
                '--force' => $this->option('force') ?? false,
            ]) === self::SUCCESS
        );
    }

    protected function publishController(): void
    {
        $this->components->task(
            'Publishing dashboard controller',
            fn() => $this->callSilent('vendor:publish', [
                '--tag' => 'activity-dashboard-controller',
                '--force' => $this->option('force') ?? false,
            ]) === self::SUCCESS
        );
    }

    protected function publishRoutes(): void
    {
        $this->components->task(
            'Publishing dashboard routes',
            fn() => $this->callSilent('vendor:publish', [
                '--tag' => 'activity-dashboard-routes',
                '--force' => $this->option('force') ?? false,
            ]) === self::SUCCESS
        );
    }

    protected function registerRouteFile(): void
    {
        $webRoutesPath = base_path('routes/web.php');
        $includeLine = "require_once base_path('routes/activity-dashboard.php');";

        if (! file_exists($webRoutesPath)) {
            $this->components->warn('Could not find routes/web.php. Please include routes/activity-dashboard.php manually.');

            return;
        }

        $contents = (string) file_get_contents($webRoutesPath);

        if (str_contains($contents, $includeLine)) {
            $this->components->task('Registering dashboard routes in routes/web.php', fn () => true);

            return;
        }

        $separator = str_ends_with($contents, "\n") ? "" : "\n";
        $updatedContents = $contents.$separator."\n".$includeLine."\n";

        file_put_contents($webRoutesPath, $updatedContents);

        $this->components->task('Registering dashboard routes in routes/web.php', fn () => true);
    }
}
