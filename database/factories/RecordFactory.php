<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\Part;
use App\Models\Country;
use App\Models\Election;
use App\Models\Locality;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Record>
 */
class RecordFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $eligible_voters_permanent = fake()->randomNumber(4);
        $eligible_voters_special = fake()->randomNumber(4);

        $present_voters_permanent = fake()->randomNumber(4);
        $present_voters_special = fake()->randomNumber(4);
        $present_voters_supliment = fake()->randomNumber(4);
        $present_voters_total = $present_voters_permanent + $present_voters_special + $present_voters_supliment;

        $papers_unused = fake()->randomNumber(5);
        $votes_null = fake()->randomNumber(4);

        $votes_valid = fake()->numberBetween(0, abs($present_voters_total - $votes_null));
        $papers_received = $papers_unused + $votes_valid + $votes_null;

        return [
            'part' => fake()->randomElement(Part::values()),
            'section' => fake()->unique()->lexify('?????????'),
            'election_id' => Election::factory(),
            'eligible_voters_permanent' => $eligible_voters_permanent,
            'eligible_voters_special' => $eligible_voters_special,
            'present_voters_permanent' => $present_voters_permanent,
            'present_voters_special' => $present_voters_special,
            'present_voters_supliment' => $present_voters_supliment,
            'papers_received' => $papers_received,
            'papers_unused' => $papers_unused,
            'votes_valid' => $votes_valid,
            'votes_null' => $votes_null,
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
