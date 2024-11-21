<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\User\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence,
            'content' => $this->faker->paragraphs(3, true),
            'published_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'author_id' => User::factory([
                'role' => Role::CONTRIBUTOR,
            ]),
        ];
    }
}
