<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ElectionType;
use App\Enums\VoteMonitorStatKey;
use App\Models\Article;
use App\Models\Candidate;
use App\Models\Country;
use App\Models\Election;
use App\Models\Locality;
use App\Models\Party;
use App\Models\Record;
use App\Models\ScheduledJob;
use App\Models\Turnout;
use App\Models\VoteMonitorStat;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Support\Collection;
use Illuminate\Support\Lottery;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Election>
 */
class ElectionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->words(3, true);
        $date = fake()->dateTimeBetween(
            '1990-01-01',
            'now'
        );

        return [
            'type' => fake()->randomElement(ElectionType::values()),
            'title' => $title,
            'slug' => Str::slug("{$title}-{$date->format('Y')}"),
            'subtitle' => Lottery::odds(1, 5)
                ->winner(fn () => fake()->word())
                ->loser(fn () => null),
            'date' => $date,
            'is_live' => false,
            'properties' => [],
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Election $election) {
            ScheduledJob::factory()
                ->for($election)
                ->enabled($election->is_live)
                ->count(2)
                ->create();

            VoteMonitorStat::factory()
                ->for($election)
                ->sequence(
                    fn (Sequence $sequence) => [
                        'key' => VoteMonitorStatKey::values()[$sequence->index],
                    ]
                )
                ->count(\count(VoteMonitorStatKey::values()))
                ->create();

            return;
            $parties = Party::factory()
                ->for($election)
                ->count(rand(10, 25))
                ->create();

            $parties->each(function (Party $party) use ($election) {
                Candidate::factory()
                    ->for($election)
                    ->party($party)
                    ->create();
            });

            Candidate::factory()
                ->for($election)
                ->count(rand(3, 10))
                ->create();
        });
    }

    public function live(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_live' => true,
            'date' => today(),
        ]);
    }

    public function withLocalTurnout(): static
    {
        return $this->afterCreating(function (Election $election) {
            Locality::query()
                ->chunkById(500, fn (Collection $localities) => Turnout::insert(
                    $localities
                        ->map(
                            fn (Locality $locality) => Turnout::factory()
                                ->for($election)
                                ->locality($locality)
                                ->make()
                        )
                        ->toArray()
                ));
        });
    }

    public function withAbroadTurnout(): static
    {
        return $this->afterCreating(function (Election $election) {
            Turnout::insert(
                Country::all()
                    ->map(
                        fn (Country $country) => Turnout::factory()
                            ->for($election)
                            ->country($country)
                            ->make()
                    )
                    ->toArray()
            );
        });
    }

    public function withNationalRecords(): static
    {
        return $this->afterCreating(function (Election $election) {
            Locality::query()
                ->chunkById(500, fn (Collection $localities) => Record::insert(
                    $localities
                        ->map(
                            fn (Locality $locality) => Record::factory()
                                ->for($election)
                                ->locality($locality)
                                ->make()
                        )
                        ->toArray()
                ));
        });
    }

    public function withDiasporaRecords(): static
    {
        return $this->afterCreating(function (Election $election) {
            Record::insert(
                Country::all()
                    ->map(
                        fn (Country $country) => Record::factory()
                            ->for($election)
                            ->country($country)
                            ->make()
                    )
                    ->toArray()
            );
        });
    }

    public function withArticles(): static
    {
        return $this->afterCreating(function (Election $election) {
            Article::factory(20)
                ->for($election)
                ->create();
        });
    }
}
