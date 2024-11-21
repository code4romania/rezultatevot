<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Election;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VoteMonitorStat>
 */
class VoteMonitorStatFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'election_id' => Election::factory(),
            'key' => null,
            'value' => fake()->numberBetween(0, 100_000),
            'enabled' => fake()->boolean(75),
        ];
    }
}
