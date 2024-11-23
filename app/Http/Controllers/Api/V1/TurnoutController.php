<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Enums\DataLevel;
use App\Http\Controllers\Controller;
use App\Http\Resources\Turnout\Diaspora\TurnoutDiasporaAggregatedResource;
use App\Http\Resources\Turnout\Diaspora\TurnoutDiasporaResource;
use App\Http\Resources\Turnout\National\TurnoutNationalAggregatedResource;
use App\Http\Resources\Turnout\TurnoutResource;
use App\Models\Country;
use App\Models\County;
use App\Models\Election;
use App\Models\Locality;
use App\Repositories\TurnoutRepository;
use Illuminate\Http\Resources\Json\JsonResource;
use stdClass;

class TurnoutController extends Controller
{
    /**
     * @operationId Total
     */
    public function total(Election $election)//: JsonResource
    {
        $result = TurnoutRepository::getForLevel(
            election: $election,
            level: DataLevel::TOTAL,
            aggregate: true,
            toBase: true,
        );

        $result->demographics = TurnoutRepository::getDemographicsForLevel(
            election: $election,
            level: DataLevel::TOTAL,
            aggregate: true,
            toBase: true,
        );

        return TurnoutResource::make($result);
    }

    /**
     * @operationId Diaspora
     */
    public function diaspora(Election $election): JsonResource
    {
        $countries = Country::pluck('name', 'id');

        $result = TurnoutRepository::getForLevel(
            election: $election,
            level: DataLevel::DIASPORA,
            toBase: true,
            aggregate: true,
        );

        $demographics = TurnoutRepository::getDemographicsForLevel(
            election: $election,
            level: DataLevel::DIASPORA,
            toBase: true,
        )->keyBy('place');

        $result->places = TurnoutRepository::getForLevel(
            election: $election,
            level: DataLevel::DIASPORA,
            toBase: true,
        )->map(function (stdClass $turnout) use ($countries, $demographics) {
            $turnout->name = $countries->get($turnout->place);

            $turnout->demographics = $demographics->get($turnout->place);

            return $turnout;
        });

        $result->demographics = TurnoutRepository::getDemographicsForLevel(
            election: $election,
            level: DataLevel::DIASPORA,
            aggregate: true,
            toBase: true,
        );

        return TurnoutDiasporaAggregatedResource::make($result);
    }

    /**
     * @operationId DiasporaCountry
     */
    public function country(Election $election, Country $country): JsonResource
    {
        $result = TurnoutRepository::getForLevel(
            election: $election,
            level: DataLevel::DIASPORA,
            country: $country->id,
            toBase: true,
        )->first();

        $result->name = $country->name;

        $result->demographics = TurnoutRepository::getDemographicsForLevel(
            election: $election,
            level: DataLevel::DIASPORA,
            country: $country->id,
            aggregate: true,
            toBase: true,
        );

        return TurnoutDiasporaResource::make($result);
    }

    /**
     * @operationId National
     */
    public function national(Election $election): JsonResource
    {
        $counties = County::pluck('name', 'id');

        $result = TurnoutRepository::getForLevel(
            election: $election,
            level: DataLevel::NATIONAL,
            aggregate: true,
            toBase: true,
        );

        $result->places = TurnoutRepository::getForLevel(
            election: $election,
            level: DataLevel::NATIONAL,
            toBase: true,
        )->map(function (stdClass $turnout) use ($counties) {
            $turnout->name = $counties->get($turnout->place);

            return $turnout;
        });

        return TurnoutNationalAggregatedResource::make($result);
    }

    /**
     * @operationId NationalCounty
     */
    public function county(Election $election, County $county): JsonResource
    {
        $result = TurnoutRepository::getForLevel(
            election: $election,
            level: DataLevel::NATIONAL,
            county: $county->id,
            aggregate: true,
            toBase: true,
        );

        $places = TurnoutRepository::getForLevel(
            election: $election,
            level: DataLevel::NATIONAL,
            county: $county->id,
            toBase: true,
        );

        $localities = Locality::query()
            ->whereIn('id', $places->pluck('place'))
            ->pluck('name', 'id');

        $result->places = $places->map(function (stdClass $turnout) use ($localities) {
            $turnout->name = $localities->get($turnout->place);

            return $turnout;
        });

        return TurnoutNationalAggregatedResource::make($result);
    }
}
