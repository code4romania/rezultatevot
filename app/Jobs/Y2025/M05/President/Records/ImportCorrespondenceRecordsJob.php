<?php

declare(strict_types=1);

namespace App\Jobs\Y2025\M05\President\Records;

use App\Jobs\SchedulableJob;
use App\Models\Record;
use App\Models\Vote;
use App\Services\RecordService;
use Illuminate\Bus\Batchable;
use League\Csv\Reader;

class ImportCorrespondenceRecordsJob extends SchedulableJob
{
    use Batchable;

    public static function name(): string
    {
        return '2025-05-04 / Prezidențiale / Procese Verbale Corespondență';
    }

    public function execute(): void
    {
        $disk = $this->scheduledJob->disk();

        $disk->put('correspondence.csv', $this->scheduledJob->fetchSource()->resource());

        $reader = Reader::createFromStream($disk->readStream('correspondence.csv'));
        $reader->setHeaderOffset(0);

        $records = collect();

        $votables = RecordService::generateVotables(
            $reader->getHeader(),
            $this->scheduledJob->election_id
        );

        foreach ($reader->getRecords() as $row) {
            $countryId = RecordService::getCountryId($row['uat_name']);
            $section = "C-{$row['precinct_nr']}";

            $part = RecordService::getPart($row['report_stage_code']);

            $records->push([
                'election_id' => $this->scheduledJob->election_id,
                'country_id' => $countryId,
                'section' => $section,
                'part' => $part,

                'eligible_voters_permanent' => $row['a'],
                'eligible_voters_special' => 0,

                'present_voters_permanent' => 0,
                'present_voters_special' => 0,
                'present_voters_supliment' => 0,
                'present_voters_mail' => $row['c'],

                'votes_valid' => $row['d1'],
                'votes_null' => $row['d2'],

                'papers_received' => 0,
                'papers_unused' => 0,

                'has_issues' => false,
            ]);

            $votes = collect();
            foreach ($votables as $column => $votable) {
                $votes->push([
                    'election_id' => $this->scheduledJob->election_id,
                    'country_id' => $countryId,
                    'section' => $section,
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
            'correspondence',
        ];
    }
}
