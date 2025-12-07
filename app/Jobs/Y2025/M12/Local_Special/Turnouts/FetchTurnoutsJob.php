<?php

declare(strict_types=1);

namespace App\Jobs\Y2025\M12\Local_Special\Turnouts;

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
        return '2025-12-07 / LOCALE SPECIALE / Prezență';
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

        $counties = County::query()->whereIn('id',
           [
               //București
               403,
               //BZ
               109,
               //BT
               74,
               //BH
               56,
               //CS
               118,
               //CT
               136,
               //DB
               154,
               //DJ
               163,
               //IL
               216,
                //IS
               225,
               //MM
               243,
               //SB
               323


           ])->get();

        $electionName = $this->scheduledJob->election->getFilamentName();
        $electionId = $this->scheduledJob->election_id;

        $time = now()->toDateTimeString();

        $jobs = $counties
            ->map(fn (County $county) => new ImportCountyTurnoutsJob($this->scheduledJob, $county));

        $persistAndClean = fn () => Bus::chain([
            new PersistTemporaryTableData(Turnout::class, $electionId),
            new DeleteTemporaryTableData(Turnout::class, $electionId),
        ])->dispatch();

        Bus::batch($jobs)
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
