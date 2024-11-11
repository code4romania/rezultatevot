<?php

declare(strict_types=1);

namespace App\Concerns;

use App\Enums\DataLevel;
use Illuminate\Database\Eloquent\Builder;
use Tpetry\QueryExpressions\Language\Alias;
use Tpetry\QueryExpressions\Value\Value;

trait CanGroupByDataLevel
{
    public function scopeForDataLevel(Builder $query, DataLevel $level, ?string $country = null, ?int $county = null, ?int $locality = null, bool $aggregate = false): Builder
    {
        if ($level->is(DataLevel::TOTAL)) {
            $query->groupByTotal();
        }

        if ($level->is(DataLevel::DIASPORA)) {
            $query->whereNotNull('country_id')
                ->when($country, fn (Builder $query) => $query->where('country_id', $country));

            if (! $aggregate) {
                $query->groupByCountry();
            }
        }

        if ($level->is(DataLevel::NATIONAL)) {
            $query->whereNull('country_id');

            if (filled($locality)) {
                $query->where('locality_id', $locality)
                    ->groupByLocality();
            } elseif (filled($county)) {
                $query->where('county_id', $county);

                if ($aggregate) {
                    $query->groupByCounty();
                } else {
                    $query->groupByLocality();
                }
            } else {
                $query->whereNotNull('locality_id')
                    ->whereNotNull('county_id');

                if ($aggregate) {
                    $query->groupByTotal();
                } else {
                    $query->groupByCounty();
                }
            }
        }

        return $query;
    }

    public function scopeGroupByCountry(Builder $query): Builder
    {
        return $query->groupBy('country_id')
            ->addSelect(new Alias('country_id', 'place'));
    }

    public function scopeGroupByCounty(Builder $query): Builder
    {
        return $query->groupBy('county_id')
            ->addSelect(new Alias('county_id', 'place'));
    }

    public function scopeGroupByLocality(Builder $query): Builder
    {
        return $query->groupBy('locality_id')
            ->addSelect(new Alias('locality_id', 'place'));
    }

    public function scopeGroupByTotal(Builder $query): Builder
    {
        return $query->addSelect(new Alias(new Value(0), 'place'));
    }
}
