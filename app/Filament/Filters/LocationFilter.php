<?php

declare(strict_types=1);

namespace App\Filament\Filters;

use App\Models\Locality;
use Filament\Forms\Components\Select;
use Filament\Forms\Get;
use Filament\Tables\Filters\BaseFilter;
use Illuminate\Database\Eloquent\Builder;

class LocationFilter extends BaseFilter
{
    protected bool $withCountry = true;

    protected bool $withCounty = true;

    protected bool $withLocality = true;

    public static function getDefaultName(): string
    {
        return 'location';
    }

    public function setUp(): void
    {
        $this->form([
            Select::make('country')
                ->label(__('app.field.country'))
                ->relationship('country', 'name')
                ->searchable()
                ->preload()
                ->visible($this->withCountry),

            Select::make('county')
                ->label(__('app.field.county'))
                ->relationship('county', 'name')
                ->searchable()
                ->preload()
                ->visible($this->withCounty),

            Select::make('locality')
                ->label(__('app.field.locality'))
                ->relationship('locality', 'name', function (Builder $query, Get $get) {
                    debug($get('county'));

                    return $query->orderBy('name');
                })
                ->getSearchResultsUsing(function (string $search, Get $get) {
                    $countyId = (int) $get('county');

                    if (! $countyId) {
                        return [];
                    }

                    return Locality::search($search)
                        ->where('county_id', $countyId)
                        ->get();
                })
                ->searchable()
                ->visible($this->withCounty && $this->withLocality),

        ]);
    }

    public function withoutCountry(): self
    {
        $this->withCountry = false;

        return $this;
    }

    public function withoutCounty(): self
    {
        $this->withCounty = false;

        return $this;
    }

    public function withoutLocality(): self
    {
        $this->withLocality = false;

        return $this;
    }
}
