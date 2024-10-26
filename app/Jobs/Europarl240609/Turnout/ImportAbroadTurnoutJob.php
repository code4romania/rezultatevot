<?php

declare(strict_types=1);

namespace App\Jobs\Europarl240609\Turnout;

use App\Exceptions\CountryCodeNotFoundException;
use App\Exceptions\MissingSourceFileException;
use App\Models\Country;
use App\Models\ScheduledJob;
use App\Models\Turnout;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use League\Csv\Reader;
use Throwable;

class ImportAbroadTurnoutJob implements ShouldQueue
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

        $records = $reader->getRecords();
        foreach ($records as $record) {
            try {
                $values->push([
                    'election_id' => $this->scheduledJob->election_id,
                    'country_id' => $this->getCountryId($record['UAT']),
                    'section' => $record['Nr sectie de votare'],

                    'initial_permanent' => $record['Înscriși pe liste permanente'],
                    'initial_complement' => 0,
                    'permanent' => $record['LP'],
                    'complement' => $record['LC'],
                    'supplement' => $record['LS'],
                    'mobile' => $record['UM'],

                    'area' => $record['Mediu'],

                    ...$segments->map(fn (string $segment) => $record[$segment]),
                ]);
            } catch (CountryCodeNotFoundException $th) {
                logger()->info($th->getMessage());
            } catch (Throwable $th) {
                // TODO: filament notification
            }
        }

        Turnout::saveToTemporaryTable($values->all());
    }

    protected function getCountryId(string $name): string
    {
        $country = Country::search($name)->first();

        if (! $country) {
            throw new CountryCodeNotFoundException($name);
        }

        return $country->id;
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
