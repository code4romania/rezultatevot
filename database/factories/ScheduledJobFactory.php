<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\Cron;
use App\Jobs\DummyJob;
use App\Models\Election;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ScheduledJob>
 */
class ScheduledJobFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'job' => DummyJob::class,
            'cron' => fake()->randomElement(Cron::values()),

            'is_enabled' => fake()->boolean(),
            'election_id' => Election::factory(),
        ];
    }

    public function enabled(bool $condition = true): static
    {
        return $this->state([
            'is_enabled' => $condition,
        ]);
    }
}
