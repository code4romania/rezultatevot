<?php

declare(strict_types=1);

namespace App\Jobs\Europarl240609\Results;

use App\Exceptions\MissingSourceFileException;
use App\Models\County;
use App\Models\Result;
use App\Models\ScheduledJob;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use League\Csv\Reader;

class ImportCountyResultsJob implements ShouldQueue
{
    use Batchable;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public ScheduledJob $scheduledJob;

    public County $county;

    public function tries(): int
    {
        return 5;
    }

    public function backoff(): array
    {
        return [1, 5, 10, 20, 30];
    }

    public function __construct(ScheduledJob $scheduledJob, County $county)
    {
        $this->scheduledJob = $scheduledJob;
        $this->county = $county;
    }

    public function handle(): void
    {
        $disk = $this->scheduledJob->disk();
        $path = $this->scheduledJob->getSourcePath("{$this->county->code}.csv");

        $disk->put(
            $path,
            $this->scheduledJob
                ->fetchSource(['{{county}}' => Str::lower($this->county->code)])
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
            $values->push([
                'election_id' => $this->scheduledJob->election_id,
                'county_id' => $this->county->id,
                'locality_id' => $record['uat_siruta'],
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
        }

        Result::saveToTemporaryTable($values->all());
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
            'county:' . $this->county->code,
        ];
    }
}
