<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Enums\DataLevel;
use App\Http\Controllers\Controller;
use App\Http\Resources\Result\ResultResource;
use App\Models\Country;
use App\Models\County;
use App\Models\Election;
use App\Models\Locality;
use App\Repositories\RecordsRepository;
use App\Repositories\VotesRepository;
use Illuminate\Http\Resources\Json\JsonResource;

class ResultController extends Controller
{
    /**
     * @operationId ResultTotal
     */
    public function total(Election $election): JsonResource
    {
        $result = RecordsRepository::getForLevel(
            election: $election,
            level: DataLevel::TOTAL,
            aggregate: true,
            toBase: true,
        );

        $result->votes = VotesRepository::getForLevel(
            election: $election,
            level: DataLevel::TOTAL,
            aggregate: true,
            toBase: true,
        );

        $result->last_updated_at = $election->records_updated_at;

        $result->name = DataLevel::TOTAL->getLabel();

        return ResultResource::make($result);
    }

    /**
     * @operationId ResultDiaspora
     */
    public function diaspora(Election $election): JsonResource
    {
        $result = RecordsRepository::getForLevel(
            election: $election,
            level: DataLevel::DIASPORA,
            aggregate: true,
            toBase: true,
        );

        $result->votes = VotesRepository::getForLevel(
            election: $election,
            level: DataLevel::DIASPORA,
            aggregate: true,
            toBase: true,
        );

        $result->last_updated_at = $election->records_updated_at;

        $result->name = DataLevel::DIASPORA->getLabel();

        return ResultResource::make($result);
    }

    /**
     * @operationId ResultCountry
     */
    public function country(Election $election, Country $country): JsonResource
    {
        $result = RecordsRepository::getForLevel(
            election: $election,
            level: DataLevel::DIASPORA,
            country: $country->id,
            aggregate: true,
            toBase: true,
        );

        $result->votes = VotesRepository::getForLevel(
            election: $election,
            level: DataLevel::DIASPORA,
            country: $country->id,
            aggregate: true,
            toBase: true,
        );

        $result->last_updated_at = $election->records_updated_at;

        $result->name = $country->name;

        return ResultResource::make($result);
    }

    /**
     * @operationId ResultNational
     */
    public function national(Election $election): JsonResource
    {
        $result = RecordsRepository::getForLevel(
            election: $election,
            level: DataLevel::NATIONAL,
            aggregate: true,
            toBase: true,
        );

        $result->votes = VotesRepository::getForLevel(
            election: $election,
            level: DataLevel::NATIONAL,
            aggregate: true,
            toBase: true,
        );

        $result->last_updated_at = $election->records_updated_at;

        $result->name = DataLevel::NATIONAL->getLabel();

        return ResultResource::make($result);
    }

    /**
     * @operationId ResultCounty
     */
    public function county(Election $election, County $county): JsonResource
    {
        $result = RecordsRepository::getForLevel(
            election: $election,
            level: DataLevel::NATIONAL,
            county: $county->id,
            aggregate: true,
            toBase: true,
        );

        $result->votes = VotesRepository::getForLevel(
            election: $election,
            level: DataLevel::NATIONAL,
            county: $county->id,
            aggregate: true,
            toBase: true,
        );

        $result->last_updated_at = $election->records_updated_at;

        $result->name = $county->name;

        return ResultResource::make($result);
    }

    /**
     * @operationId ResultLocality
     */
    public function locality(Election $election, County $county, Locality $locality): JsonResource
    {
        $result = RecordsRepository::getForLevel(
            election: $election,
            level: DataLevel::NATIONAL,
            county: $county->id,
            locality: $locality->id,
            aggregate: true,
            toBase: true,
        );

        $result->votes = VotesRepository::getForLevel(
            election: $election,
            level: DataLevel::NATIONAL,
            county: $county->id,
            locality: $locality->id,
            aggregate: true,
            toBase: true,
        );

        $result->last_updated_at = $election->records_updated_at;

        $result->level = DataLevel::NATIONAL;
        $result->name = $county->name . ' / ' . $locality->name;

        return ResultResource::make($result);
    }
}
