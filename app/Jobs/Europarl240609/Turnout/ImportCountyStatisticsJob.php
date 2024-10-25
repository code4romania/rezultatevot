<?php

declare(strict_types=1);

namespace App\Jobs\Europarl240609\Turnout;

use App\Exceptions\MissingSourceFileException;
use App\Models\County;
use App\Models\ScheduledJob;
use App\Models\Statistic;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\SkipIfBatchCancelled;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use League\Csv\Reader;

class ImportCountyStatisticsJob implements ShouldQueue
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
            throw new MissingSourceFileException($path);
        }

        $reader = Reader::createFromStream($disk->readStream($path));
        $reader->setHeaderOffset(0);

        logger()->info('ImportCountyStatisticsJob', [
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
                'area' => $record['Mediu'],
                'section' => $record['Nr sectie de votare'],
                ...Statistic::segments()
                    ->mapWithKeys(function (array $segment) use ($record) {
                        $gender = match ($segment[0]) {
                            'men' => 'Barbati',
                            'women' => 'Femei',
                        };

                        return [
                            "{$segment[0]}_{$segment[1]}" => data_get($record, "{$gender} {$segment[1]}", 0),
                        ];
                    })
                    ->all(),
            ]);
        }

        $values->chunk(200)
            ->each(fn (Collection $chunk) => Statistic::upsert(
                $chunk->all(),
                ['election_id', 'county_id', 'section'],
            ));
    }

    public function middleware(): array
    {
        return [new SkipIfBatchCancelled];
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
            'statistics',
            'scheduled_job:' . $this->scheduledJob->id,
            'election:' . $this->scheduledJob->election_id,
            'county:' . $this->county->code,
        ];
    }
}
