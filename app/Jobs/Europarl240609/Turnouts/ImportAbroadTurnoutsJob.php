<?php

declare(strict_types=1);

namespace App\Jobs\Europarl240609\Turnouts;

use App\Events\CountryCodeNotFound;
use App\Exceptions\CountryCodeNotFoundException;
use App\Exceptions\MissingSourceFileException;
use App\Models\Country;
use App\Models\ScheduledJob;
use App\Models\Turnout;
use App\Services\RecordService;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use League\Csv\Reader;

class ImportAbroadTurnoutsJob implements ShouldQueue
{
    use Batchable;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public ScheduledJob $scheduledJob;

    public function __construct(ScheduledJob $scheduledJob)
    {
        $this->scheduledJob = $scheduledJob;
    }

    public function handle(): void
    {
        $disk = $this->scheduledJob->disk();
        $path = $this->scheduledJob->getSourcePath('SR.csv');

        if (! $disk->exists($path)) {
            throw new MissingSourceFileException($path);
        }

        $reader = Reader::createFromStream($disk->readStream($path));
        $reader->setHeaderOffset(0);

        $values = collect();

        $segments = Turnout::segmentsMap();

        foreach ($reader->getRecords() as $record) {
            try {
                $values->push([
                    'election_id' => $this->scheduledJob->election_id,
                    'country_id' => $this->getCountryId($record['UAT']),
                    'section' => $record['Nr sectie de votare'],

                    'initial_permanent' => $record['Înscriși pe liste permanente'],
                    'initial_complement' => 0,
                    'permanent' => $record['LP'],
                    'complement' => $record['LC'],
                    'supplement' => $record['LS'],
                    'mobile' => $record['UM'],

                    'area' => $record['Mediu'],
                    'has_issues' => RecordService::checkTurnout($record),

                    ...$segments->map(fn (string $segment) => $record[$segment]),
                ]);
            } catch (CountryCodeNotFoundException $th) {
                CountryCodeNotFound::dispatch($record['UAT'], $this->scheduledJob->election);
            }
        }

        Turnout::saveToTemporaryTable($values->all());
    }

    protected function determineIfHasIssues(array $record): bool
    {
        $computedTotal = collect(['LP', 'LC', 'LS', 'UM'])
            ->map(fn (string $key) => $record[$key])
            ->sum();

        if ($computedTotal !== $record['LT']) {
            return true;
        }

        return false;
    }

    protected function getCountryId(string $name): string
    {
        $country = Country::search($name)->first();

        if (! $country) {
            throw new CountryCodeNotFoundException($name);
        }

        return $country->id;
    }

    /**
     * Get the tags that should be assigned to the job.
     *
     * @return array<int, string>
     */
    public function tags(): array
    {
        return [
            'import',
            'turnout',
            'scheduled_job:' . $this->scheduledJob->id,
            'election:' . $this->scheduledJob->election_id,
            'abroad',
        ];
    }
}
