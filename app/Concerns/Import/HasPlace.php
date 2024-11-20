<?php

declare(strict_types=1);

namespace App\Concerns\Import;

use App\Models\Country;
use App\Models\County;
use App\Models\Locality;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use stdClass;

trait HasPlace
{
    protected ?Collection $countries = null;

    protected ?Collection $counties = null;

    protected ?Collection $localities = null;

    protected function getCountries(): Collection
    {
        $countries = collect();

        Country::each(function (Country $country) use ($countries) {
            collect($country->old_ids)->each(
                fn (int $oldId) => $countries->put($oldId, $country->id)
            );
        });

        return $countries;
    }

    protected function getCounties(): Collection
    {
        return County::pluck('id', 'old_id');
    }

    protected function getLocalities(): Collection
    {
        $localities = collect();

        Locality::query()
            ->whereNotNull('old_ids')
            ->each(function (Locality $locality) use ($localities) {
                collect($locality->old_ids)->each(
                    fn (int $oldId) => $localities->put($oldId, $locality->id)
                );
            });

        return $localities;
    }

    protected function getPlace(stdClass $row): ?array
    {
        if (blank($this->countries)) {
            $this->countries = $this->getCountries();
        }

        if (blank($this->counties)) {
            $this->counties = $this->getCounties();
        }

        if (blank($this->localities)) {
            $this->localities = $this->getLocalities();
        }

        $place = [
            'country_id' => $this->countries->get($row->CountryId),
            'county_id' => $this->counties->get($row->CountyId),
            'locality_id' => $this->localities->get($row->LocalityId),
        ];

        $validation = Validator::make($place, [
            'country_id' => ['required_without:county_id,locality_id'],
            'county_id' => ['required_without:country_id', 'required_with:locality_id'],
            'locality_id' => ['required_without:country_id', 'required_with:county_id'],
        ]);

        if ($validation->fails()) {
            if ($place['county_id'] === 403 && blank($place['locality_id'])) {
                // TODO: Date doar pe București, TBD
                // old ballot ids 62, 63, 64, 65, 69, 69, 71, 72, 73, 74
            } elseif (
                blank($place['locality_id']) &&
                $row->LocalityId >= 64413 &&
                $row->LocalityId <= 64497
            ) {
                // TODO: Toate Localitatile Din Judet
                // old ballot ids 71, 72, 73, 74
            } else {
                logger()->error('Could not determine location.', [
                    'BallotId' => $row->BallotId,
                    'TurnoutId' => $row->Id,
                    'CountyId' => $row->CountyId,
                    'LocalityId' => $row->LocalityId,
                    'locality_id' => $place['locality_id'],
                ]);

                return null;
            }
        }

        return $place;
    }
}
