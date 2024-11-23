<?php

declare(strict_types=1);

namespace App\Http\Resources\Turnout\National;

use App\Http\Resources\Turnout\AreaResource;
use App\Http\Resources\Turnout\DemographicsResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TurnoutNationalResource extends JsonResource
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
             * Uat id referenced in nomenclature.
             * @var string
             */
            'code' => $this->place,

            /*
             * Uat name.
             * @var string
             */
            'name' => $this->name,

            /*
             * Demographics data.
             * @var DemographicsResource
             */
            'demographics' => DemographicsResource::make($this->demographics),

            /*
             * Areas data.
             * @var AreaResource
             */
            'areas' => AreaResource::make($this->areas),
        ];
    }
}
