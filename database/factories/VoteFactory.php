<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\Part;
use App\Models\Country;
use App\Models\Election;
use App\Models\Locality;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vote>
 */
class VoteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'part' => fake()->randomElement(Part::values()),
            'section' => fake()->unique()->lexify('?????????'),
            'election_id' => Election::factory(),
            'votes' => fake()->randomNumber(4),
        ];
    }

    public function locality(Locality $locality): static
    {
        return $this->state(fn () => [
            'locality_id' => $locality->id,
            'county_id' => $locality->county_id,
        ]);
    }

    public function country(Country $country): static
    {
        return $this->state(fn () => [
            'country_id' => $country->id,
        ]);
    }
}
