<?php

declare(strict_types=1);

namespace App\Http\Resources\Turnout\Diaspora;

use App\Http\Resources\Turnout\AreaResource;
use App\Http\Resources\Turnout\DemographicsResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TurnoutDiasporaResource extends JsonResource
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
             * Country code in ISO2.
             * @var string
             */
            'code' => $this->place,

            /*
             * Country name.
             * @var string
             */
            'name' => $this->name,

            'demographics' => DemographicsResource::make($this->demographics),

            'areas' => AreaResource::make($this->areas),

            'last_update' => $this->whenHas('last_update', $this->last_update),


        ];
    }
}
