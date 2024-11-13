<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Enums\DataLevel;
use App\Http\Controllers\Controller;
use App\Http\Resources\Turnout\Diaspora\TurnoutDiasporaAggregatedResource;
use App\Http\Resources\Turnout\Diaspora\TurnoutDiasporaResource;
use App\Http\Resources\Turnout\National\TurnoutNationalAggregatedResource;
use App\Http\Resources\Turnout\National\TurnoutNationalResource;
use App\Http\Resources\Turnout\TurnoutResource;
use App\Models\Country;
use App\Models\County;
use App\Models\Election;
use App\Models\Turnout;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

class TurnoutController extends Controller
{
    public function total(Election $election): JsonResponse
    {
        $result = Turnout::query()
            ->whereBelongsTo($election)
            ->forLevel(
                level: DataLevel::TOTAL,
            )
            ->first();

        return response()->json(TurnoutResource::make($result));
    }

    public function diaspora(Election $election): JsonResponse
    {
        $general = Turnout::query()
            ->whereBelongsTo($election)
            ->forLevel(
                level: DataLevel::DIASPORA,
                aggregate: true,
            )
            ->first();

        $general->uats = Turnout::query()
            ->whereBelongsTo($election)
            ->forLevel(
                level: DataLevel::DIASPORA,
            )
            ->toBase()
            ->get()
            ->toArray();

        return response()->json(TurnoutDiasporaAggregatedResource::make($general));
    }

    public function country(Election $election, Country $country): JsonResource
    {
        return TurnoutDiasporaResource::make(
            Turnout::query()
                ->whereBelongsTo($election)
                ->forLevel(
                    level: DataLevel::DIASPORA,
                    country: $country->id,
                )
                ->first()
        );
    }

    public function national(Election $election): JsonResponse
    {
        $result = Turnout::query()
            ->whereBelongsTo($election)
            ->forLevel(
                level: DataLevel::NATIONAL,
                aggregate: true,
            )
            ->toBase()
            ->first();

        $result->uats = Turnout::query()->whereBelongsTo($election)
            ->forLevel(
                level: DataLevel::NATIONAL,
            )
            ->toBase()
            ->get()
            ->toArray();

        return response()->json(TurnoutNationalAggregatedResource::make($result));
    }

    public function county(Election $election, County $county): JsonResponse
    {
        return response()->json(
            TurnoutNationalResource::make(
                Turnout::query()
                    ->whereBelongsTo($election)
                    ->forLevel(
                        level: DataLevel::NATIONAL,
                        county: $county->id,
                    )
                    ->toBase()
                    ->first()
            )
        );
    }
}
