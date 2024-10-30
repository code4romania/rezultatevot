<?php

declare(strict_types=1);

namespace App\Jobs\Europarl240609\Results;

use App\Jobs\DeleteTemporaryTableData;
use App\Jobs\PersistTemporaryTableData;
use App\Jobs\SchedulableJob;
use App\Models\County;
use App\Models\Result;
use Illuminate\Support\Facades\Bus;

class FetchResultsJob extends SchedulableJob
{
    public static function name(): string
    {
        return 'Europarlamentare 09.06.2024 / Rezultate';
    }

    public function execute(): void
    {
        $counties = County::all();

        $electionName = $this->scheduledJob->election->getFilamentName();
        $electionId = $this->scheduledJob->election_id;

        $time = now()->toDateTimeString();

        $jobs = $counties
            ->map(fn (County $county) => new ImportCountyResultsJob($this->scheduledJob, $county))
            ->push(new ImportAbroadResultsJob($this->scheduledJob));

        $persistAndClean = fn () => Bus::chain([
            new PersistTemporaryTableData(Result::class, $electionId),
            new DeleteTemporaryTableData(Result::class, $electionId),
        ])->dispatch();

        Bus::batch($jobs)
            ->catch($persistAndClean)
            ->then($persistAndClean)
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
            'results',
            'scheduled_job:' . $this->scheduledJob->id,
            'election:' . $this->scheduledJob->election_id,
            static::name(),
        ];
    }
}
