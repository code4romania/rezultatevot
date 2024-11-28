<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Enums\DataLevel;
use App\Http\Controllers\Controller;
use App\Http\Resources\Result\ResultResource;
use App\Models\Candidate;
use App\Models\Election;
use App\Models\Party;
use App\Models\Vote;
use App\Repositories\VotesRepository;
use Illuminate\Http\Resources\Json\JsonResource;
use stdClass;

class ResultController extends Controller
{
    /**
     * @operationId ResultTotal
     */
    public function total(Election $election): JsonResource
    {
        $result = VotesRepository::getMapDataForLevel(
            election: $election,
            level: DataLevel::TOTAL,
        )->mapWithKeys(function (stdClass $vote) {
            return [
                $vote->place => [
                    'value' => percent($vote->votes, $vote->total_votes, formatted: true),
                    'type' => $vote->votable_type,
                    'id' => $vote->votable_id,
                ],
            ];
        });



        return ResultResource::make();
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
