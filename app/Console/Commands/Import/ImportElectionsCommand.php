<?php

declare(strict_types=1);

namespace App\Console\Commands\Import;

use App\Enums\ElectionType;
use App\Models\Election;
use Carbon\Carbon;
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
            $type = match ($row->BallotType) {
                0 => ElectionType::REFERENDUM,
                1 => ElectionType::PRESIDENTIAL,
                2,3 => ElectionType::PARLIAMENTARY,
                7 => ElectionType::EURO,
                default => ElectionType::LOCAL,
            };

            $date = Carbon::parse($row->Date);

            $slug = match ($type) {
                ElectionType::PRESIDENTIAL => "prezidentiale-{$row->Name}-{$date->year}",
                ElectionType::EURO => "europarlamentare {$date->year}",
                default => "{$row->Name}-{$date->year}",
            };

            Election::create([
                'title' => $row->Name,
                'slug' => $slug,
                'type' => $type,
                'date' => $date->toDateString(),
                'is_live' => false,
                'has_lists' => match ($row->BallotType) {
                    // Referendum = 0,
                    // President = 1,
                    // Senate = 2,
                    // House = 3,
                    // LocalCouncil = 4,
                    // CountyCouncil = 5,
                    // Mayor = 6,
                    // EuropeanParliament = 7,
                    // CountyCouncilPresident = 8,
                    // CapitalCityMayor = 9,
                    // CapitalCityCouncil = 10,
                    0, 1, 6, 8, 9 => false,
                    2, 3, 4, 5, 7, 10 => true,
                },
                'old_id' => $row->BallotId,
            ]);
        }, (int) $this->option('chunk'));

        $this->finishProgressBar('Imported elections');

        return static::SUCCESS;
    }
}
