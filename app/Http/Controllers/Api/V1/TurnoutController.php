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
use App\Models\Turnout;
use Illuminate\Http\Resources\Json\JsonResource;

class TurnoutController extends Controller
{
    /**
     * @operationId Total
     */
    public function total(Election $election): JsonResource
    {
        $result = Turnout::query()
            ->whereBelongsTo($election)
            ->forLevel(
                level: DataLevel::TOTAL,
            )
            ->first();

        return TurnoutResource::make($result);
    }

    /**
     * @operationId Diaspora
     */
    public function diaspora(Election $election): JsonResource
    {
        $general = Turnout::query()
            ->whereBelongsTo($election)
            ->forLevel(
                level: DataLevel::DIASPORA,
                aggregate: true,
            )
            ->first();

        $general->places = Turnout::query()
            ->whereBelongsTo($election)
            ->forLevel(
                level: DataLevel::DIASPORA,
            )
            ->addSelect('country_id')
            ->get()
            ->append('name');

        return TurnoutDiasporaAggregatedResource::make($general);
    }

    /**
     * @operationId DiasporaCountry
     */
    public function country(Election $election, Country $country): JsonResource
    {
        return TurnoutDiasporaResource::make(
            Turnout::query()
                ->whereBelongsTo($election)
                ->forLevel(
                    level: DataLevel::DIASPORA,
                    country: $country->id,
                )
                ->addSelect('country_id')
                ->first()
        );
    }

    /**
     * @operationId National
     */
    public function national(Election $election): JsonResource
    {
        $result = Turnout::query()
            ->whereBelongsTo($election)
            ->forLevel(
                level: DataLevel::NATIONAL,
                aggregate: true,
            )
            ->first();

        $result->places = Turnout::query()->whereBelongsTo($election)
            ->forLevel(
                level: DataLevel::NATIONAL,
            )
            ->addSelect('county_id')
            ->get();

        return TurnoutNationalAggregatedResource::make($result);
    }

    /**
     * @operationId NationalCounty
     */
    public function county(Election $election, County $county): JsonResource
    {
        $countyData = Turnout::query()
            ->whereBelongsTo($election)
            ->forLevel(
                level: DataLevel::NATIONAL,
                county: $county->id,
                aggregate: true
            )
            ->first();

        $countyData->places = Turnout::query()
            ->whereBelongsTo($election)
            ->forLevel(
                level: DataLevel::NATIONAL,
                county: $county->id,
            )
            ->addSelect('locality_id')
            ->get()
            ->append('name');

        return TurnoutNationalAggregatedResource::make($countyData);
    }
}
