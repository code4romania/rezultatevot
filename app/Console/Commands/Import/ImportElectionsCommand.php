<?php

declare(strict_types=1);

namespace App\Console\Commands\Import;

use App\Enums\ElectionType;
use App\Models\Election;
use stdClass;

class ImportElectionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import:elections
        {--chunk=100 : The number of records to process at a time}
        {--force : Force the operation to run when in production}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import elections from the old database.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (! $this->confirmToProceed()) {
            return static::FAILURE;
        }

        $query = $this->db
            ->table('ballots')
            ->orderBy('ballots.Date');

        $this->createProgressBar(
            'Importing elections...',
            $query->count()
        );

        $query->each(function (stdClass $row) {
            Election::create([
                'title' => $row->Name,
                'type' => match ($row->BallotType) {
                    0 => ElectionType::REFERENDUM,
                    1 => ElectionType::PRESIDENTIAL,
                    2,3 => ElectionType::PARLIAMENTARY,
                    7 => ElectionType::EURO,
                    default => ElectionType::LOCAL,
                },
                'date' => $row->Date,
                'is_live' => false,
                'old_id' => $row->BallotId,
            ]);
        }, (int) $this->option('chunk'));

        $this->finishProgressBar('Imported elections');

        return static::SUCCESS;
    }
}
