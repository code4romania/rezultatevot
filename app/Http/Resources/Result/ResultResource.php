<?php

declare(strict_types=1);

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

            /*
             *  @var  \DateTime $last_updated_at
             */
            'last_updated_at' => data_get($this->resource, 'last_updated_at')?->toDateTimeString(),

            /*
             *  @var  string $name
             */

            'name' => $this->name,

            /*
             *  @var  int $eligible_voters_total
             */
            'eligible_voters_total' => (int) $this->eligible_voters_total,

            /*
             *  @var  int $present_voters_total
             */
            'present_voters_total' => (int) $this->present_voters_total,

            /*
             * @var  int $votes_valid
             * Number of valid votes
             */
            'votes_valid' => (int) $this->votes_valid,

            /*
             * @var  int $votes_null
             * Number of null votes
             */
            'votes_null' => (int) $this->votes_null,

            'candidates' => CandidatesResource::collection($this->votes),
        ];
    }
}
