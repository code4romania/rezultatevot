<?php

declare(strict_types=1);

namespace App\Jobs\Y2024\M06\EuropeanParliament\Records;

use App\Jobs\DeleteTemporaryTableData;
use App\Jobs\PersistTemporaryTableData;
use App\Jobs\SchedulableJob;
use App\Jobs\UpdateElectionRecordsTimestamp;
use App\Models\County;
use App\Models\Record;
use App\Models\Vote;
use Illuminate\Support\Facades\Bus;

class FetchRecordsJob extends SchedulableJob
{
    public static function name(): string
    {
        return '2024-06-09 / Europarlamentare / Procese Verbale';
    }

    public function execute(): void
    {
        $counties = County::all();

        $electionName = $this->scheduledJob->election->getFilamentName();
        $electionId = $this->scheduledJob->election_id;

        $time = now()->toDateTimeString();

        $jobs = $counties
            ->map(fn (County $county) => new ImportCountyRecordsJob($this->scheduledJob, $county))
            ->push(new ImportAbroadRecordsJob($this->scheduledJob));

        $persistAndClean = fn () => Bus::chain([
            new PersistTemporaryTableData(Record::class, $electionId),
            new DeleteTemporaryTableData(Record::class, $electionId),

            new PersistTemporaryTableData(Vote::class, $electionId),
            new DeleteTemporaryTableData(Vote::class, $electionId),
        ])->dispatch();

        Bus::batch($jobs)
            ->catch($persistAndClean)
            ->then($persistAndClean)
            ->then(fn () => UpdateElectionRecordsTimestamp::dispatch($electionId))
            ->name("$electionName / Rezultate / $time")
            ->allowFailures()
            ->dispatch();
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
            static::name(),
        ];
    }
}
