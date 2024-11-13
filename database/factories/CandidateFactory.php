<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Election;
use App\Models\Party;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Candidate>
 */
class CandidateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->name(),
            'color' => fake()->boolean(25) ? fake()->hexColor() : null,
            'election_id' => Election::factory(),
        ];
    }

    public function party(Party $party): static
    {
        return $this->state(fn () => [
            'party_id' => $party->id,
        ]);
    }
}
