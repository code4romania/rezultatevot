<?php

declare(strict_types=1);

namespace App\Console\Commands\Import;

use App\Models\User;
use Illuminate\Database\Console\Migrations\FreshCommand;

class ImportPrepareCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import:prepare {--force : Force the operation to run when in production}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove old data and prepare for import.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (! $this->confirmToProceed()) {
            return static::FAILURE;
        }

        $this->createProgressBar('Removing old data...', 2);

        $this->callSilent(FreshCommand::class);
        $this->progressBar->advance();

        User::factory(['email' => 'admin@example.com'])
            ->admin()
            ->create();

        $this->finishProgressBar('Removed old data');

        return static::SUCCESS;
    }
}
