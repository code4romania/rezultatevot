<?php

declare(strict_types=1);

namespace App\Http\Resources\Turnout\Diaspora;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TurnoutDiasporaAggregatedResource extends JsonResource
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
             * Total number of voters subscribed to the election permanent list .
             * @var integer
             */
            'initial_total' => $this->initial_total,

            /*
             * Total number of people who voted in the election.
             * @var integer
             */
            'total' => $this->total,

            /*
             * Array of countries and their respective turnout.
             * @var TurnoutDiasporaResource[]
             */
            'countries' => TurnoutDiasporaResource::collection($this->uats),

        ];
    }
}
