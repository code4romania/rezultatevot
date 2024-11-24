<?php

declare(strict_types=1);

namespace App\Jobs\ReferendumBucuresti241124\Turnouts;

use App\Jobs\DeleteTemporaryTableData;
use App\Jobs\PersistTemporaryTableData;
use App\Jobs\SchedulableJob;
use App\Jobs\UpdateElectionTurnoutsTimestamp;
use App\Models\County;
use App\Models\Turnout;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Spatie\TemporaryDirectory\TemporaryDirectory;

class FetchTurnoutsJob extends SchedulableJob
{
    public static function name(): string
    {
        return 'Referendum Bucuresti 24.11.2024 / Prezență';
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

        $time = now()->toDateTimeString();

        $persistAndClean = fn () => Bus::chain([
            new PersistTemporaryTableData(Turnout::class, $electionId),
            new DeleteTemporaryTableData(Turnout::class, $electionId),
        ])->dispatch();

        Bus::batch([new ImportTurnoutsJob($this->scheduledJob, County::find(403))])
            ->catch($persistAndClean)
            ->then($persistAndClean)
            ->then(fn () => UpdateElectionTurnoutsTimestamp::dispatch($electionId))
            ->name("$electionName / Prezență / $time")
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
            'turnout',
            'scheduled_job:' . $this->scheduledJob->id,
            'election:' . $this->scheduledJob->election_id,
            static::name(),
        ];
    }
}
