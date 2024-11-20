<?php

declare(strict_types=1);

namespace App\Console\Commands\Import;

use App\Concerns\Import\HasPlace;
use App\Enums\Part;
use App\Models\Country;
use App\Models\Election;
use App\Models\Locality;
use App\Models\Record;
use App\Models\Turnout;
use Illuminate\Support\Collection;
use stdClass;

class ImportTurnoutsCommand extends Command
{
    use HasPlace;

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
            ->leftJoin('countries', 'turnouts.CountryId', '=', 'countries.Id')
            ->select([
                'turnouts.*',
                'localities.Name as LocalityName',
                'countries.Name as CountryName',
            ]);

        $this->createProgressBar(
            'Importing turnouts...',
            $query->count()
        );

        Election::all()->each(function (Election $election) use ($query) {
            $index = 1;

            (clone $query)
                ->where('turnouts.BallotId', $election->old_id)
                ->chunk((int) $this->option('chunk'), function (Collection $rows) use ($election, &$index) {
                    $turnouts = [];
                    $records = [];

                    $rows->each(function (stdClass $row) use ($election, &$index, &$turnouts, &$records) {
                        $place = $this->getPlace($row);

                        $section = $index++;

                        if (blank($place)) {
                            return;
                        }

                        $turnouts[] = [
                            'election_id' => $election->id,
                            'section' => $section,

                            'initial_permanent' => $row->EligibleVoters,
                            'initial_complement' => 0,

                            'permanent' => $row->TotalVotes,
                            'complement' => 0, //$row->SpecialListsVotes,
                            'supplement' => 0, //$row->SuplimentaryVotes,
                            'mobile' => 0, //max($row->VotesByMail, $row->CorrespondenceVotes),
                            ...$place,
                        ];

                        $records[] = [
                            'election_id' => $election->id,
                            'section' => $section,
                            'part' => Part::FINAL,

                            'eligible_voters_permanent' => $row->EligibleVoters,
                            'eligible_voters_special' => 0,

                            'present_voters_permanent' => $row->TotalVotes,
                            'present_voters_special' => 0,
                            'present_voters_supliment' => 0,

                            'papers_received' => 0,
                            'papers_unused' => 0,
                            'votes_valid' => $row->ValidVotes,
                            'votes_null' => $row->NullVotes,

                            ...$place,
                        ];
                    });

                    Turnout::insert($turnouts);

                    Record::insert($records);

                    $this->progressBar->advance($rows->count());
                });
        });

        $this->finishProgressBar('Imported turnouts');

        return static::SUCCESS;
    }
}
