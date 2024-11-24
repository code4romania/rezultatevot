<?php

declare(strict_types=1);

namespace App\Jobs\ReferendumBucuresti241124\Records;

use App\Jobs\DeleteTemporaryTableData;
use App\Jobs\PersistTemporaryTableData;
use App\Jobs\SchedulableJob;
use App\Jobs\UpdateElectionRecordsTimestamp;
use App\Models\County;
use App\Models\Record;
use App\Models\Vote;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Spatie\TemporaryDirectory\TemporaryDirectory;

class FetchRecordsJob extends SchedulableJob
{
    public static function name(): string
    {
        return 'Referendum Bucuresti 24.11.2024 / Procese Verbale';
    }

    public function execute(): void
    {
        $temporaryDirectory = TemporaryDirectory::make()
            ->deleteWhenDestroyed();

        $cwd = $temporaryDirectory->path();

        $tmpDisk = Storage::build([
            'driver' => 'local',
            'root' => $cwd,
        ]);

        $tmpDisk->put('turnout.csv', $this->scheduledJob->fetchSource()->resource());

        // Split the CSV by county
        Process::path($cwd)
            ->run([
                config('import.awk_path'),
                '-F,',
                'FNR==1 {header = $0; next} !seen[$1]++ {print header > $1".csv"} {print > $1".csv"}',
                'turnout.csv',
            ]);

        $tmpDisk->delete('turnout.csv');

        collect($tmpDisk->allFiles())
            ->each(function (string $file) use ($tmpDisk) {
                $this->scheduledJob->disk()
                    ->writeStream(
                        $this->scheduledJob->getSourcePath($file),
                        $tmpDisk->readStream($file)
                    );
            });

        $electionName = $this->scheduledJob->election->getFilamentName();
        $electionId = $this->scheduledJob->election_id;

        $sourceFiles = collect([
            '42' => 'B',
        ]);

        $jobs = $sourceFiles
            ->map(fn (string $countyCode, string $filename) => new ImportRecordsJob($this->scheduledJob, $countyCode, $filename));

        $time = now()->toDateTimeString();

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
