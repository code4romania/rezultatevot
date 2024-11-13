<?php

declare(strict_types=1);

namespace App\Http\Resources\Nomenclature;

use App\Models\County;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CountyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /* @var County $this */
        return [
            'id' => $this->id,
            'name' => $this->name,
            'localities' => LocalityResource::collection($this->whenLoaded('localities')),
        ];
    }
}
