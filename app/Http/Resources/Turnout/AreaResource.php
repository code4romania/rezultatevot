<?php

declare(strict_types=1);

namespace App\Http\Resources\Turnout;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AreaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = collect($this->resource)->pluck('total', 'area');

        return [
            'urban' => (int) $data->get('U', 0),
            'rural' => (int) $data->get('R', 0),
        ];
    }
}
