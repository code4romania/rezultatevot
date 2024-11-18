<?php

declare(strict_types=1);

namespace App\Console\Commands\Import;

class ImportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import
        {--force : Force the operation to run when in production}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run all the importers in order.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (! $this->confirmToProceed()) {
            return static::FAILURE;
        }

        $this->call(ImportPrepareCommand::class, [
            '--force' => $this->option('force'),
        ]);

        $this->call(ImportElectionsCommand::class, [
            '--force' => $this->option('force'),
        ]);

        $this->call(ImportOldIdsCommand::class, [
            '--force' => $this->option('force'),
        ]);
        $this->call(ImportTurnoutsCommand::class, [
            '--force' => $this->option('force'),
        ]);

        return self::SUCCESS;
    }
}
