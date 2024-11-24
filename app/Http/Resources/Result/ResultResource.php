<?php

namespace App\Http\Resources\Result;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResultResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            /**
             *  @var  int $eligible_voters_total
             */
//            'eligible_voters_total' => 0,
//            'eligible_voters_permanent' => 0,
//            'eligible_voters_special' => 0,
//            'present_voters_total' => 0,
//            'present_voters_permanent' => 0,
//            'present_voters_special' => 0,
//            'present_voters_supliment' => 0,
//            'papers_received' => 0,
//            'papers_unused' => 0,
//

            /**
         * @var  int $votes_valid
         * Number of valid votes
         */
            'votes_valid' => 0,

            /**
             * @var  int $votes_null
             * Number of null votes
             */
            'votes_null' => 0,

            /**
             * @var  int $votes_total
             * Total number of votes null + valid
             */
            'votes_total' => 0,

            /**
             * @var  int $votes_total
             * Candidates list with votes and percentage
             */
            'candidates' => CandidatesResource::collection([]),
        ];
    }
}
