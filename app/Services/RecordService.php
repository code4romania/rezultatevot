<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\Part;
use App\Enums\Time;
use App\Exceptions\CountryCodeNotFoundException;
use App\Models\Candidate;
use App\Models\Country;
use App\Models\Party;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class RecordService
{
    public static function checkTurnout(array $record): bool
    {
        $computedTotal = collect(['LP', 'LC', 'LS', 'UM'])
            ->map(fn (string $key) => $record[$key])
            ->sum();

        if ($computedTotal !== $record['LT']) {
            return true;
        }

        return false;
    }

    public static function checkRecord(array $record): bool
    {
        if ($record['a'] != $record['a1'] + $record['a2']) {
            return true;
        }

        if ($record['a1'] < $record['b1']) {
            return true;
        }

        if ($record['a2'] < $record['b2']) {
            return true;
        }

        if ($record['b'] != $record['b1'] + $record['b2'] + $record['b3']) {
            return true;
        }

        if ($record['c'] < $record['d'] + $record['e'] + $record['f']) {
            return true;
        }

        return false;
    }

    public static function getPart(string $code): Part
    {
        return match (Str::lower($code)) {
            'final' => Part::FINAL,
            'part' => Part::PART,
            'prov' => Part::PROV,
        };
    }

    public static function getCountryId(string $name): string
    {
        $country = Country::search($name)->first();

        if (! $country) {
            throw new CountryCodeNotFoundException($name);
        }

        return $country->id;
    }

    public static function isIndependentCandidate(string $name): bool
    {
        return Str::contains($name, config('import.independent_candidate_designation'), ignoreCase: true);
    }

    public static function getName(string $name): string
    {
        return Str::of($name)
            ->remove(config('import.independent_candidate_designation'), caseSensitive: false)
            ->trim('-')
            ->trim()
            ->value();
    }

    public static function generateVotables(array $header, int $electionId): Collection
    {
        return Cache::tags(['votables'])->remember(
            hash('xxh128', implode(',', $header)),
            Time::MINUTE_IN_SECONDS,
            fn () => collect($header)
                ->filter(fn (string $column) => Str::endsWith($column, config('import.candidate_votes_suffix')))
                ->mapWithKeys(function (string $column) use ($electionId) {
                    $name = Str::before($column, config('import.candidate_votes_suffix'));

                    $votable = Party::query()
                        ->where('name', $name)
                        ->where('election_id', $electionId)
                        ->firstOr(
                            fn () => Candidate::query()
                                ->where('name', static::getName($name))
                                ->where('election_id', $electionId)
                                ->first()
                        );

                    if (blank($votable)) {
                        throw new \Exception("Votable not found for column: {$name}");
                    }

                    return [
                        $column => [
                            'votable_type' => $votable?->getMorphClass(),
                            'votable_id' => $votable?->id,
                        ],
                    ];
                })
        );
    }
}
