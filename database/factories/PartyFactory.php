<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Election;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Party>
 */
class PartyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(5, true);

        return [
            'name' => $name,
            'acronym' => Str::initials($name),
            'color' => fake()->hexColor(),
            'election_id' => Election::factory(),
        ];
    }
}
