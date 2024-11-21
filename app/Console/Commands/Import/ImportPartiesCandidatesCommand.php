<?php

declare(strict_types=1);

namespace App\Console\Commands\Import;

use App\Concerns\Import\HasPlace;
use App\Enums\ElectionType;
use App\Enums\Part;
use App\Models\Candidate;
use App\Models\Election;
use App\Models\Party;
use App\Models\Vote;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use stdClass;

class ImportPartiesCandidatesCommand extends Command
{
    use HasPlace;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import:parties-candidates
        {--chunk=1000 : The number of records to process at a time}
        {--force : Force the operation to run when in production}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import place ids from the old database.';

    private ?Collection $parties = null;

    private ?Collection $candidates = null;

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (! $this->confirmToProceed()) {
            return static::FAILURE;
        }

        Schema::withoutForeignKeyConstraints(function () {
            Candidate::truncate();
            Party::truncate();
            Vote::truncate();
        });

        $partyList = $this->getPartyList();

        Election::query()
            ->withoutGlobalScopes()
            ->each(function (Election $election) use ($partyList) {
                $index = 1;

                $this->importPartiesAndCandidates($election, $partyList);
                $this->importVotes($election, $index);
            });

        // $this->parse

        return static::SUCCESS;
    }

    protected function getPartyList(): Collection
    {
        return $this->db
            ->table('parties')
            ->orderBy('parties.Id')
            ->get()
            ->keyBy('Id')
            ->each(function (stdClass $row) {
                if (filled($row->LogoUrl)) {
                    $path = 'parties/' . $row->Id . '.' . pathinfo($row->LogoUrl, \PATHINFO_EXTENSION);

                    if (! Storage::disk('local')->exists($path)) {
                        Storage::disk('local')
                            ->put($path, file_get_contents($row->LogoUrl));
                    }
                }

                return $row;
            });
    }

    protected function importPartiesAndCandidates(Election $election, Collection $partyList): void
    {
        $this->parties = collect();
        $this->candidates = collect();

        $query = $this->db
            ->table('candidateresults')
            ->select(['Name', 'ShortName', 'PartyName', 'PartyId'])
            ->distinct()
            ->where('BallotId', $election->old_id)
            ->whereIn('Division', [3, 4])
            ->orderBy('Name');

        $this->createProgressBar(
            "Importing candidates and parties for election #{$election->id}...",
            $query->count()
        );

        $query->each(function (stdClass $row) use ($election, $partyList) {
            $candidate = $party = null;

            if (filled($row->PartyId)) {
                $party = $this->parties->get($row->PartyId, function () use ($election, $row, $partyList) {
                    $item = $partyList->get($row->PartyId);

                    /** @var Party */
                    $p = $election->parties()->create([
                        'name' => $item->Name,
                        'acronym' => $item->ShortName,
                        'color' => $item->Color,
                    ]);

                    if (filled($item->LogoUrl)) {
                        $ext = pathinfo($item->LogoUrl, \PATHINFO_EXTENSION);
                        $p->addMediaFromDisk("{$item->Id}.{$ext}", 'local');
                    }

                    $this->parties->put($row->PartyId, $p->only('id', 'name'));

                    return $p;
                });
            }

            $candidateName = $row->Name ?: $row->ShortName;

            if (Str::slug($candidateName) !== Str::slug(data_get($party, 'name'))) {
                $candidate = $election->candidates()->create([
                    'name' => $candidateName,
                    'party_id' => data_get($party, 'id'),
                ]);

                $this->candidates->push($candidate->only('id', 'name', 'party_id'));
            }

            $this->progressBar->advance();
        }, (int) $this->option('chunk'));

        $this->finishProgressBar("Imported candidates and parties for election #{$election->id}.");
    }

    protected function importVotes(Election $election, int &$index): void
    {
        $query = $this->db
            ->table('candidateresults')
            ->where('BallotId', $election->old_id)
            ->whereIn('Division', [3, 4])
            ->orderBy('candidateresults.Id')
            ->leftJoin('localities', 'candidateresults.LocalityId', '=', 'localities.LocalityId')
            ->leftJoin('countries', 'candidateresults.CountryId', '=', 'countries.Id')
            ->select([
                'candidateresults.*',
                'localities.Name as LocalityName',
                'countries.Name as CountryName',
            ]);

        $this->createProgressBar(
            "Importing votes for election #{$election->id}...",
            $query->count()
        );

        $query->chunk((int) $this->option('chunk'), function (Collection $rows) use ($election, &$index) {
            // TODO: implement
            if ($election->type->is(ElectionType::REFERENDUM)) {
                return;
            }

            Vote::insert(
                $rows
                    ->map(function (stdClass $row) use ($election, &$index) {
                        $place = $this->getPlace($row);

                        $section = $index++;

                        if (blank($place)) {
                            return null;
                        }

                        $partyName = $row->PartyName;
                        $candidateName = $row->Name ?: $row->ShortName;

                        if ($election->has_lists) {
                            $votable = filled($row->PartyId)
                                ? $this->getParty($partyName)
                                : $this->getCandidate($candidateName);
                        } else {
                            $votable = $this->getCandidate($candidateName);
                        }

                        if (blank($votable)) {
                            throw new Exception("Votable id not found for {$candidateName}.");
                        }

                        return [
                            'election_id' => $election->id,
                            'section' => $section,
                            'part' => Part::FINAL,

                            'votes' => $row->Votes,
                            ...$votable,
                            ...$place,
                        ];
                    })
                    ->filter()
                    ->toArray()
            );

            $this->progressBar->advance($rows->count());
        });

        $this->finishProgressBar("Imported votes for election #{$election->id}.");
    }

    protected function getParty(string $name): ?array
    {
        $party = $this->parties
            ->firstWhere(fn (array $party) => Str::slug($party['name']) === Str::slug($name));

        if (blank($party)) {
            return null;
        }

        return [
            'votable_type' => (new Party)->getMorphClass(),
            'votable_id' => $party['id'],
        ];
    }

    protected function getCandidate(string $name): ?array
    {
        $candidate = $this->candidates
            ->firstWhere(fn (array $candidate) => Str::slug($candidate['name']) === Str::slug($name));

        if (blank($candidate)) {
            return null;
        }

        return [
            'votable_type' => (new Candidate)->getMorphClass(),
            'votable_id' => $candidate['id'],
        ];
    }
}
