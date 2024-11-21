<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\Part;
use App\Models\Candidate;
use App\Models\Country;
use App\Models\Election;
use App\Models\Locality;
use App\Models\Party;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Mandate>
 */
class MandateFactory extends Factory
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
            'mandates' => fake()->randomNumber(3),
            'party_id' => Party::factory(),
        ];
    }

    public function votable(Candidate|Party $votable): static
    {
        return $this->state(fn () => [
            'votable_type' => $votable->getMorphClass(),
            'votable_id' => $votable->id,
        ]);
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
