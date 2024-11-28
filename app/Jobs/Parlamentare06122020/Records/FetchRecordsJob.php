<?php

declare(strict_types=1);

namespace App\Jobs\Parlamentare06122020\Records;

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
        return 'Parlamentare 06.12.2020 / Procese Verbale';
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

        $sourceFiles = collect([
            '1' => 'AB',
            '2' => 'AR',
            '3' => 'AG',
            '4' => 'BC',
            '5' => 'BH',
            '6' => 'BN',
            '7' => 'BT',
            '8' => 'BV',
            '9' => 'BR',
            '10' => 'BZ',
            '11' => 'CS',
            '12' => 'CL',
            '13' => 'CJ',
            '14' => 'CT',
            '15' => 'CV',
            '16' => 'DB',
            '17' => 'DJ',
            '18' => 'GL',
            '19' => 'GR',
            '20' => 'GJ',
            '21' => 'HR',
            '22' => 'HD',
            '23' => 'IL',
            '24' => 'IS',
            '25' => 'IF',
            '26' => 'MM',
            '27' => 'MH',
            '28' => 'MS',
            '29' => 'NT',
            '30' => 'OT',
            '31' => 'PH',
            '32' => 'SM',
            '33' => 'SJ',
            '34' => 'SB',
            '35' => 'SV',
            '36' => 'TR',
            '37' => 'TM',
            '38' => 'TL',
            '39' => 'VS',
            '40' => 'VL',
            '41' => 'VN',

            '44' => 'B',
            '45' => 'B',
            '46' => 'B',
            '47' => 'B',
            '48' => 'B',
            '49' => 'B',
        ]);

        $jobs = $sourceFiles
            ->map(fn (string $countyCode, string $filename) => new ImportCountyRecordsJob($this->scheduledJob, $countyCode, $filename))
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
