<?php

declare(strict_types=1);

namespace App\Console\Commands\Import;

use App\Models\Country;
use App\Models\County;
use App\Models\Locality;
use stdClass;

class ImportOldIdsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import:oldIds
        {--chunk=1000 : The number of records to process at a time}
        {--force : Force the operation to run when in production}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import place ids from the old database.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (! $this->confirmToProceed()) {
            return static::FAILURE;
        }

        $this->importCountryIds();
        $this->importCountyIds();
        $this->importLocalityIds();

        return static::SUCCESS;
    }

    protected function importCountryIds(): void
    {
        $query = $this->db
            ->table('countries')
            ->orderBy('countries.Id');

        $this->createProgressBar(
            'Importing country IDs...',
            $query->count()
        );

        $query->each(function (stdClass $row) {
            $name = match ($row->Name) {
                'Geneva' => 'Elveția',
                default => $row->Name,
            };

            $country = Country::search($name)->first();

            if (blank($country)) {
                logger()->error("Country not found: {$name}");

                return;
            }

            $oldIds = $country->old_ids ?? [];
            $oldIds[] = $row->Id;

            $country->updateQuietly([
                'old_ids' => $oldIds,
            ]);

            $this->progressBar->advance();
        }, (int) $this->option('chunk'));

        $this->finishProgressBar('Imported country IDs');
    }

    protected function importCountyIds(): void
    {
        $query = $this->db
            ->table('counties')
            ->orderBy('counties.ShortName');

        $this->createProgressBar(
            'Importing county IDs...',
            $query->count()
        );

        $counties = County::all()
            ->keyBy('code');

        County::upsert(
            $query
                ->get()
                ->map(fn (stdClass $row) => [
                    ...$counties->get($row->ShortName)->toArray(),
                    'code' => $row->ShortName,
                    'old_id' => $row->CountyId,
                ])
                ->all(),
            uniqueBy: ['code'],
            update: ['old_id']
        );

        $this->finishProgressBar('Imported county IDs');
    }

    protected function importLocalityIds(): void
    {
        $query = $this->db
            ->table('localities')
            ->orderBy('localities.LocalityId');

        $this->createProgressBar(
            'Importing locality IDs...',
            $query->count()
        );

        $counties = County::pluck('id', 'old_id');

        $query->each(function (stdClass $row) use ($counties) {
            if ($row->Siruta === 0) {
                $locality = $this->searchLocalities($row->Name, $counties->get($row->CountyId));
            } else {
                $locality = Locality::query()
                    ->where('id', $row->Siruta)
                    ->firstOr(fn () => $this->searchLocalities($row->Name, $counties->get($row->CountyId)));
            }

            // logger()->info("{$row->LocalityId} | {$row->Name} | Siruta: {$row->Siruta} => " . $locality?->name ?? 'NULL');

            if (blank($locality)) {
                logger()->error("Locality not found: {$row->LocalityId} | {$row->Name} | Siruta: {$row->Siruta}");

                return;
            }

            $oldIds = $locality->old_ids ?? [];
            $oldIds[] = $row->LocalityId;

            $locality->updateQuietly([
                'old_ids' => $oldIds,
            ]);

            $this->progressBar->advance();
        }, (int) $this->option('chunk'));

        $this->finishProgressBar('Imported locality IDs');
    }

    protected function searchLocalities(string $name, int $county_id): ?Locality
    {
        $name = match (true) {
            $county_id === 118 && $name === 'Pescari' => 'Coronini',
            $county_id === 136 && $name === 'Basarabi' => 'Murfatlar',
            $county_id === 207 && $name === 'Unirea' => 'General Berthelot',
            default => $name,
        };

        return Locality::search($name)
            ->where('county_id', $county_id)
            ->get()
            ->sortBy('parent_id')
            ->first();
    }
}
