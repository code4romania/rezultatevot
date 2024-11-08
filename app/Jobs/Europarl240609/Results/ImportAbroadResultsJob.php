<?php

declare(strict_types=1);

namespace App\Jobs\Europarl240609\Results;

use App\Events\CountryCodeNotFound;
use App\Exceptions\CountryCodeNotFoundException;
use App\Exceptions\MissingSourceFileException;
use App\Models\Country;
use App\Models\Record;
use App\Models\ScheduledJob;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use League\Csv\Reader;

class ImportAbroadResultsJob implements ShouldQueue
{
    use Batchable;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public ScheduledJob $scheduledJob;

    public function tries(): int
    {
        return 5;
    }

    public function backoff(): array
    {
        return [1, 5, 10, 20, 30];
    }

    public function __construct(ScheduledJob $scheduledJob)
    {
        $this->scheduledJob = $scheduledJob;
    }

    public function handle(): void
    {
        $disk = $this->scheduledJob->disk();
        $path = $this->scheduledJob->getSourcePath('sr.csv');

        $disk->put(
            $path,
            $this->scheduledJob
                ->fetchSource(['{{county}}' => 'sr'])
                ->resource()
        );

        if (! $disk->exists($path)) {
            throw new MissingSourceFileException($path);
        }

        $reader = Reader::createFromStream($disk->readStream($path));
        $reader->setHeaderOffset(0);

        $values = collect();

        $records = $reader->getRecords();
        foreach ($records as $record) {
            try {
                $values->push([
                    'election_id' => $this->scheduledJob->election_id,
                    'country_id' => $this->getCountryId($record['uat_name']),
                    'section' => $record['precinct_nr'],

                    'eligible_voters_permanent' => $record['a1'],
                    'eligible_voters_special' => $record['a2'],

                    'present_voters_permanent' => $record['b1'],
                    'present_voters_special' => $record['b2'],
                    'present_voters_supliment' => $record['b3'],

                    'papers_received' => $record['c'],
                    'papers_unused' => $record['d'],
                    'votes_valid' => $record['e'],
                    'votes_null' => $record['f'],

                    'has_issues' => $this->determineIfHasIssues($record),
                ]);
            } catch (CountryCodeNotFoundException $th) {
                CountryCodeNotFound::dispatch($record['uat_name'], $this->scheduledJob->election);
            }
        }

        Record::saveToTemporaryTable($values->all());
    }

    protected function determineIfHasIssues(array $record): bool
    {
        if ($record['a'] != $record['a1'] + $record['a2']) {
            return true;
        }

        if ($record['a1'] < $record['b1']) {
            return true;
        }

        if ($record['a2'] < $record['b2']) {
            return true;
        }

        if ($record['b'] != $record['b1'] + $record['b2'] + $record['b3']) {
            return true;
        }

        if ($record['c'] < $record['d'] + $record['e'] + $record['f']) {
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
            'results',
            'scheduled_job:' . $this->scheduledJob->id,
            'election:' . $this->scheduledJob->election_id,
            'abroad',
        ];
    }
}
