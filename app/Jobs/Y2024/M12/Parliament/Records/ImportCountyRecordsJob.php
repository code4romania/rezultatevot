<?php

declare(strict_types=1);

namespace App\Jobs\Y2024\M12\Parliament\Records;

use App\Exceptions\MissingSourceFileException;
use App\Models\County;
use App\Models\Record;
use App\Models\ScheduledJob;
use App\Models\Vote;
use App\Services\RecordService;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use League\Csv\Reader;

class ImportCountyRecordsJob implements ShouldQueue, ShouldBeUnique
{
    use Batchable;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $tries = 1;

    public ScheduledJob $scheduledJob;

    public County $county;

    public string $filename;

    public function __construct(ScheduledJob $scheduledJob, string $countyCode, string $filename)
    {
        $this->scheduledJob = $scheduledJob;
        $this->county = County::where('code', $countyCode)->first();
        $this->filename = $filename;
    }

    public function handle(): void
    {
        $disk = $this->scheduledJob->disk();
        $path = $this->scheduledJob->getSourcePath("{$this->filename}.csv");

        if (! $disk->exists($path)) {
            // throw new MissingSourceFileException($path);
            logger()->warning('Missing source file', ['path' => $path]);

            return;
        }

        $reader = Reader::createFromStream($disk->readStream($path));
        $reader->setHeaderOffset(0);

        $records = collect();

        $votables = RecordService::generateVotables(
            $reader->getHeader(),
            $this->scheduledJob->election_id
        );

        foreach ($reader->getRecords() as $row) {
            $part = RecordService::getPart($row['report_stage_code']);

            $records->push([
                'election_id' => $this->scheduledJob->election_id,
                'county_id' => $this->county->id,
                'locality_id' => $row['uat_siruta'],
                'section' => $row['precinct_nr'],
                'part' => $part,

                'eligible_voters_permanent' => $row['a'],
                'eligible_voters_special' => 0,

                'present_voters_permanent' => $row['b1'],
                'present_voters_special' => $row['b2'],
                'present_voters_supliment' => $row['b3'],
                'present_voters_mail' => 0, //$row['b4'],

                'votes_valid' => $row['e'],
                'votes_null' => $row['f'],

                'papers_received' => $row['c'],
                'papers_unused' => $row['d'],

                'has_issues' => false,
            ]);

            $votes = collect();

            foreach ($votables as $column => $votable) {
                $votes->push([
                    'election_id' => $this->scheduledJob->election_id,
                    'county_id' => $this->county->id,
                    'locality_id' => $row['uat_siruta'],
                    'section' => $row['precinct_nr'],
                    'part' => $part,

                    'votable_type' => $votable['votable_type'],
                    'votable_id' => $votable['votable_id'],

                    'votes' => $row[$column],
                ]);
            }

            Vote::saveToTemporaryTable($votes->all());
        }

        Record::saveToTemporaryTable($records->all());
    }

    public function uniqueId(): string
    {
        return "{$this->scheduledJob->election_id}-{$this->county->code}";
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
            'county:' . $this->county->code,
        ];
    }
}
