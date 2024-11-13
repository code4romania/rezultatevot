<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\Time;
use App\Models\Candidate;
use App\Models\Party;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class GenerateMappedVotablesList
{
    public function votables(array $header): Collection
    {
        return Cache::remember(
            hash('xxh128', implode(',', $header)),
            Time::MINUTE_IN_SECONDS,
            fn () => collect($header)
                ->filter(fn (string $column) => $this->hasVotesSuffix($column))
                ->mapWithKeys(fn (string $column) => [
                    $column => $this->getCandidateOrParty($column),
                ])
        );
    }

    protected function getVotesSuffix(): string
    {
        return config('import.candidate_votes_suffix');
    }

    protected function hasVotesSuffix(string $column): bool
    {
        return Str::endsWith($column, $this->getVotesSuffix());
    }

    protected function getCandidateOrParty(string $name): array
    {
        $name = Str::before($name, $this->getVotesSuffix());

        $votable = Party::query()
            ->where('name', $name)
            ->firstOr(
                fn () => Candidate::query()
                    ->where('name', $name)
                    ->first()
            );

        if (blank($votable)) {
            throw new \Exception("Votable not found for column: {$name}");
        }

        return [
            'votable_type' => $votable?->getMorphClass(),
            'votable_id' => $votable?->id,
        ];
    }
}
