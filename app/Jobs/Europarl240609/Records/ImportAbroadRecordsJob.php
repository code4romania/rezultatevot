<?php

declare(strict_types=1);

namespace App\Jobs\Europarl240609\Records;

use App\Events\CountryCodeNotFound;
use App\Exceptions\CountryCodeNotFoundException;
use App\Exceptions\MissingSourceFileException;
use App\Models\Country;
use App\Models\Record;
use App\Models\ScheduledJob;
use App\Models\Vote;
use App\Services\RecordService;
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

        $records = collect();

        $votables = RecordService::generateVotables($reader->getHeader());

        foreach ($reader->getRecords() as $row) {
            try {
                $countryId = $this->getCountryId($row['uat_name']);

                $part = RecordService::getPart($row['report_stage_code']);

                $records->push([
                    'election_id' => $this->scheduledJob->election_id,
                    'country_id' => $countryId,
                    'section' => $row['precinct_nr'],
                    'part' => $part,

                    'eligible_voters_permanent' => $row['a1'],
                    'eligible_voters_special' => $row['a2'],

                    'present_voters_permanent' => $row['b1'],
                    'present_voters_special' => $row['b2'],
                    'present_voters_supliment' => $row['b3'],

                    'papers_received' => $row['c'],
                    'papers_unused' => $row['d'],
                    'votes_valid' => $row['e'],
                    'votes_null' => $row['f'],

                    'has_issues' => RecordService::checkRecord($row),
                ]);

                $votes = collect();
                foreach ($votables as $column => $votable) {
                    $votes->push([
                        'election_id' => $this->scheduledJob->election_id,
                        'country_id' => $countryId,
                        'section' => $row['precinct_nr'],
                        'part' => $part,

                        'votable_type' => $votable['votable_type'],
                        'votable_id' => $votable['votable_id'],

                        'votes' => $row[$column],
                    ]);
                }

                Vote::saveToTemporaryTable($votes->all());
            } catch (CountryCodeNotFoundException $th) {
                CountryCodeNotFound::dispatch($row['uat_name'], $this->scheduledJob->election);
            }
        }

        Record::saveToTemporaryTable($records->all());
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
