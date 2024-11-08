<?php

declare(strict_types=1);

namespace App\Jobs\Europarl240609\Records;

use App\Actions\CheckRecordHasIssues;
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

class ImportAbroadRecordsJob implements ShouldQueue
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

    public function handle(CheckRecordHasIssues $checker): void
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

                    'has_issues' => $checker->checkRecord($record),
                ]);
            } catch (CountryCodeNotFoundException $th) {
                CountryCodeNotFound::dispatch($record['uat_name'], $this->scheduledJob->election);
            }
        }

        Record::saveToTemporaryTable($values->all());
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
            'records',
            'scheduled_job:' . $this->scheduledJob->id,
            'election:' . $this->scheduledJob->election_id,
            'abroad',
        ];
    }
}
