<?php

declare(strict_types=1);

namespace App\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Tpetry\QueryExpressions\Language\Alias;
use Tpetry\QueryExpressions\Value\Value;

trait CanGroupByPlace
{
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
