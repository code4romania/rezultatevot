<?php

declare(strict_types=1);

namespace App\Http\Resources\Result;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CandidatesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            /*
             *  @var  string $name
             * Name of the candidate
             */
            'name' => 'Candidate Name',
            /*
             *  @var  int $votes
             * Number of votes the candidate received
             */
            'votes' => 0,
            /*
             *  @var  float $percentage
             * Percentage of votes the candidate received (candidate_votes/total_votes )*100
             */
            'percentage' => 0,
        ];
    }
}
