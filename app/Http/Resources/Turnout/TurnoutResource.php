<?php

declare(strict_types=1);

namespace App\Http\Resources\Turnout;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TurnoutResource extends JsonResource
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

            'demographics' => DemographicsResource::make($this->demographics),

            'areas' => AreaResource::make($this->areas),
        ];
    }
}
