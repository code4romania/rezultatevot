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
        $this->form(function () {
            $form = [];

            if ($this->withCountry) {
                $form[] = Select::make('country')
                    ->label(__('app.field.country'))
                    ->relationship('country', 'name')
                    ->searchable()
                    ->preload();
            }

            if ($this->withCounty) {
                $form[] = Select::make('county')
                    ->label(__('app.field.county'))
                    ->relationship('county', 'name')
                    ->searchable()
                    ->preload();

                if ($this->withLocality) {
                    $form[] = Select::make('locality')
                        ->label(__('app.field.locality'))
                        ->relationship('locality', 'name', function (Builder $query, Get $get) {
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
                        ->searchable();
                }
            }

            return $form;
        })->query(function (Builder $query, array $data) {
            if ($this->withCountry && isset($data['country'])) {
                $query->where('country_id', $data['country']);
            }

            if ($this->withCounty && isset($data['county'])) {
                $query->where('county_id', $data['county']);

                if ($this->withLocality && isset($data['locality'])) {
                    $query->where('locality_id', $data['locality']);
                }
            }
        });
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
