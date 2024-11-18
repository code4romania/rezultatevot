<?php

declare(strict_types=1);

namespace App\Console\Commands\Import;

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
        //
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
            ->orderBy('localities.Siruta')
            ->where('localities.Siruta', '!=', 0);

        $this->createProgressBar(
            'Importing locality IDs...',
            $query->count()
        );

        $query->each(function (stdClass $row) {
            Locality::query()
                ->where('id', $row->Siruta)
                ->update([
                    'old_id' => $row->LocalityId,
                ]);
        }, (int) $this->option('chunk'));

        $this->finishProgressBar('Imported locality IDs');
    }
}
