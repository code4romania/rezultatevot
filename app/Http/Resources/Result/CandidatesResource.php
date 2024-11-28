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
            'name' => $this['name'],
            /*
             *  @var  int $votes
             * Number of votes the candidate received
             */
            'votes' => (int) $this['votes'],

        ];
    }
}
