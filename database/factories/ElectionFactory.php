<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Country;
use App\Models\Election;
use App\Models\ElectionType;
use App\Models\Locality;
use App\Models\Party;
use App\Models\ScheduledJob;
use App\Models\Turnout;
use Illuminate\Database\Eloquent\Factories\Factory;
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
        $year = fake()->year();

        return [
            'type_id' => ElectionType::factory(),
            'title' => $title,
            'slug' => Str::slug("$title-$year"),
            'subtitle' => Lottery::odds(1, 5)
                ->winner(fn () => fake()->word())
                ->loser(fn () => null),
            'year' => $year,
            'is_live' => false,
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Election $election) {
            Party::factory()
                ->for($election)
                ->count(rand(25, 50))
                ->create();

            ScheduledJob::factory()
                ->for($election)
                ->enabled($election->is_live)
                ->count(2)
                ->create();
        });
    }

    public function live(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_live' => true,
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
}
