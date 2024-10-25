<?php

declare(strict_types=1);

namespace App\Jobs\Europarl240609\Turnout;

use App\Jobs\SchedulableJob;
use App\Models\County;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Spatie\TemporaryDirectory\TemporaryDirectory;

class FetchTurnoutDataJob extends SchedulableJob
{
    public static function name(): string
    {
        return 'Europarlamentare 09.06.2024 / Prezență';
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

        $counties = County::all();

        $electionName = $this->scheduledJob->election->getFilamentName();

        $time = now()->toDateTimeString();

        Bus::chain([
            Bus::batch(
                $counties
                    ->map(fn (County $county) => new ImportCountyTurnoutJob($this->scheduledJob, $county))
            )->name("$electionName / Prezență / $time"),

            Bus::batch(
                $counties
                    ->map(fn (County $county) => new ImportCountyStatisticsJob($this->scheduledJob, $county))
            )->name("$electionName / Statistici / $time"),
        ])->onQueue('sequential')->dispatch();
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
