<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Country;
use App\Models\Election;
use App\Models\Locality;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Turnout>
 */
class TurnoutFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $initial_permanent = fake()->randomNumber(7);
        $initial_complement = fake()->randomNumber(7);

        return [
            'section' => fake()->unique()->lexify('?????????'),
            'election_id' => Election::factory(),
            'initial_permanent' => $initial_permanent,
            'initial_complement' => $initial_complement,
            'permanent' => fake()->numberBetween(0, $initial_permanent),
            'complement' => fake()->numberBetween(0, $initial_complement),
            'supplement' => fake()->randomNumber(5),
            'mobile' => fake()->randomNumber(5),
        ];
    }

    public function locality(Locality $locality): static
    {
        return $this->state(fn () => [
            'locality_id' => $locality->id,
        ]);
    }

    public function country(Country $country): static
    {
        return $this->state(fn () => [
            'country_id' => $country->id,
        ]);
    }
}
