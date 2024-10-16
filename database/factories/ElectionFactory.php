<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Election;
use App\Models\ElectionType;
use App\Models\Party;
use Illuminate\Database\Eloquent\Factories\Factory;
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
        });
    }

    public function live(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_live' => true,
        ]);
    }
}
