<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Enums\DataLevel;
use App\Http\Controllers\Controller;
use App\Http\Resources\Result\ResultResource;
use App\Models\Election;
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

        return ResultResource::make($result);
    }

    /**
     * @operationId ResultDiaspora
     */
    public function diaspora(): JsonResource
    {
        return ResultResource::make();
    }

    /**
     * @operationId ResultCountry
     */
    public function country(): JsonResource
    {
        return ResultResource::make();
    }

    /**
     * @operationId ResultNational
     */
    public function national(): JsonResource
    {
        return ResultResource::make();
    }

    /**
     * @operationId ResultCounty
     */
    public function county(): JsonResource
    {
        return ResultResource::make();
    }
}
