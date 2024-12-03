<?php

declare(strict_types=1);

namespace App\Jobs\Y2020\M06\Parliament\Turnouts;

use App\Exceptions\MissingSourceFileException;
use App\Models\ScheduledJob;
use App\Models\Turnout;
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
            $values->push([
                'election_id' => $this->scheduledJob->election_id,
                'country_id' => RecordService::getCountryId($record['UAT']),
                'section' => $record['Nr sectie de votare'],

                'initial_permanent' => $record['Votanti pe lista permanenta'],
                'initial_complement' => $record['Votanti pe lista complementara'],
                'permanent' => $record['LP'],
                'complement' => $record['LSC'],
                'supplement' => $record['LS'],
                'mobile' => $record['UM'],

                'area' => $record['Mediu'],
                'has_issues' => $this->determineIfHasIssues($record),

                ...$segments->map(fn (string $segment) => $record[$segment]),
            ]);
        }

        Turnout::saveToTemporaryTable($values->all());
    }

    protected function determineIfHasIssues(array $record): bool
    {
        $computedTotal = collect(['LP', 'LSC', 'LS', 'UM'])
            ->map(fn (string $key) => $record[$key])
            ->sum();

        if ($computedTotal !== $record['LT']) {
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
            'turnout',
            'scheduled_job:' . $this->scheduledJob->id,
            'election:' . $this->scheduledJob->election_id,
            'abroad',
        ];
    }
}
