<?php

declare(strict_types=1);

namespace App\Console\Commands\Import;

use App\Models\Country;
use App\Models\County;
use App\Models\Election;
use App\Models\Locality;
use App\Models\Record;
use App\Models\Turnout;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use stdClass;

class ImportTurnoutsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import:turnouts
        {--chunk=1000 : The number of records to process at a time}
        {--force : Force the operation to run when in production}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import turnouts from the old database.';

    protected ?Collection $countries = null;

    protected ?Collection $counties = null;

    protected ?Collection $localities = null;

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (! $this->confirmToProceed()) {
            return static::FAILURE;
        }

        $query = $this->db
            ->table('turnouts')
            ->whereIn('turnouts.Division', [3]) // 3 = locality, 4 = diaspora_country
            ->orderBy('turnouts.Id')
            ->leftJoin('localities', 'turnouts.LocalityId', '=', 'localities.LocalityId')
            ->select([
                '*',
                'localities.Name as LocalityName',
            ]);

        $this->createProgressBar(
            'Importing turnouts...',
            $query->count()
        );

        // $this->countries = Country::pluck('id', 'old_id');
        $this->counties = County::pluck('id', 'old_id');
        $this->localities = Locality::query()
            ->whereNull('parent_id')
            ->pluck('id', 'old_id');

        Election::all()->each(function (Election $election) use ($query) {
            $index = 1;

            (clone $query)
                ->where('turnouts.BallotId', $election->old_id)
                ->chunk((int) $this->option('chunk'), function (Collection $rows) use ($election, &$index) {
                    $turnouts = [];
                    $records = [];

                    $rows->each(function (stdClass $row) use ($election, &$index, &$turnouts, &$records) {
                        $place = $this->getPlace($row);

                        if (blank($place)) {
                            return;
                        }

                        $turnouts[] = [
                            'election_id' => $election->id,
                            'section' => $index++,

                            'initial_permanent' => $row->EligibleVoters,
                            'initial_complement' => 0,

                            'permanent' => $row->PermanentListsVotes,
                            'complement' => $row->SpecialListsVotes,
                            'supplement' => $row->SuplimentaryVotes,
                            'mobile' => 0,
                            ...$place,
                        ];

                        $records[] = [
                            ...$place,
                        ];
                    });

                    Turnout::insert($turnouts);

                    // Record::insert($records);
                });
        });

        $this->finishProgressBar('Imported turnouts');

        return static::SUCCESS;
    }

    protected function getPlace(stdClass $row): ?array
    {
        $place = [
            // 'country_id' => $this->countries->get($row->CountryId),
            'county_id' => $this->counties->get($row->CountyId),
            'locality_id' => $this->localities->get($row->LocalityId),
        ];

        $validation = Validator::make($place, [
            'country_id' => ['required_without:county_id,locality_id'],
            'county_id' => ['required_without:country_id', 'required_with:locality_id'],
            'locality_id' => ['required_without:country_id', 'required_with:county_id'],
        ]);

        if ($validation->fails()) {
            if (blank($place['locality_id'])) {
                $place['locality_id'] = match ((int) $row->LocalityId) {
                    116921 => 61069, // Băneasa, Constanța
                    713, 21469 => 9280, // Fântânele, Arad
                    default => Locality::search($row->LocalityName)
                        ->where('county_id', $place['county_id'])
                        ->first()
                        ?->id,
                };
            }

            $validation->setData($place);

            if ($validation->fails()) {
                logger()->error("Could not determine location for turnout id {$row->Id}", [
                    'BallotId' => $row->BallotId,
                    'LocalityId' => $row->LocalityId,
                    'place' => $place,
                ]);

                return null;
            }
        }

        return $place;
    }
}
