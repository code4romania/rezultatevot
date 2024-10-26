<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RebuildScoutCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scout:rebuild';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rebuilds the search index for all searchable models';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        collect(config('scout.typesense.model-settings'))
            ->keys()
            ->each(function (string $model): void {
                $this->call('scout:flush', ['model' => $model]);
                $this->call('scout:import', ['model' => $model]);
            });

        return self::SUCCESS;
    }
}
