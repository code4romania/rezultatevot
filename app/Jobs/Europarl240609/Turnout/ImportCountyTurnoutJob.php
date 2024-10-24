<?php

declare(strict_types=1);

namespace App\Jobs\Europarl240609\Turnout;

use App\Models\County;
use App\Models\ScheduledJob;
use App\Models\Turnout;
use Exception;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use League\Csv\Reader;

class ImportCountyTurnoutJob implements ShouldQueue
{
    use Batchable;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public ScheduledJob $scheduledJob;

    public County $county;

    public function __construct(ScheduledJob $scheduledJob, County $county)
    {
        $this->scheduledJob = $scheduledJob;
        $this->county = $county;
    }

    public function handle(): void
    {
        $disk = $this->scheduledJob->disk();
        $path = $this->scheduledJob->getSourcePath("{$this->county->code}.csv");

        if (! $disk->exists($path)) {
            throw new Exception('Missing source file for county ' . $this->county->code);
        }

        $reader = Reader::createFromStream($disk->readStream($path));
        $reader->setHeaderOffset(0);

        logger()->info('ImportCountyTurnoutJob', [
            'county' => $this->county->code,
            'first' => $reader->count(),
        ]);

        $values = collect();

        $records = $reader->getRecords();
        foreach ($records as $record) {
            $values->push([
                'election_id' => $this->scheduledJob->election_id,
                'county_id' => $this->county->id,
                'locality_id' => $record['Siruta'],
                'section' => $record['Nr sectie de votare'],

                'initial_permanent' => $record['Înscriși pe liste permanente'],
                'initial_complement' => 0,
                'permanent' => $record['LP'],
                'complement' => $record['LC'],
                'supplement' => $record['LS'],
                'mobile' => $record['UM'],
            ]);
        }

        Turnout::upsert(
            $values->all(),
            ['election_id', 'county_id', 'section'],
            ['initial_permanent', 'initial_complement', 'permanent', 'complement', 'supplement', 'mobile']
        );
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
            'county:' . $this->county->code,
        ];
    }
}
