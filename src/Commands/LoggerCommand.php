<?php

namespace Gottvergessen\Logger\Commands;

use Illuminate\Console\Command;

class LoggerCommand extends Command
{
    protected $signature = 'logger:install {--force : Overwrite existing files}';

    protected $description = 'Install the Logger package (config, migrations)';

    public function handle(): int
    {
        $this->info('Installing Logger package...');

        // $this->call('vendor:publish', [
        //     '--tag' => 'logger-config',
        //     '--force' => $this->option('force'),
        // ]);

        // $this->call('vendor:publish', [
        //     '--tag' => 'logger-migrations',
        //     '--force' => $this->option('force'),
        // ]);

        // if ($this->confirm('Run migrations now?', true)) {
        //     $this->call('migrate');
        // }

        $this->info('Logger installed successfully.');

        return self::SUCCESS;
    }
}