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

class ResultsController extends Controller
{
    /**
     * @operationId Results/Total
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
     * @operationId Results/Diaspora
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
     * @operationId Results/Diaspora/Country
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
     * @operationId Results/National
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
     * @operationId Results/National/County
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
     * @operationId Results/National/County/Locality
     */
    public function locality(Election $election, County $county, Locality $locality): JsonResource
    {
        abort_unless($locality->county_id === $county->id, 404);

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

        $result->name = "{$locality->name}, {$county->name}";

        return ResultResource::make($result);
    }
}
