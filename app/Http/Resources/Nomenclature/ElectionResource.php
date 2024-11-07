<?php

declare(strict_types=1);

namespace App\Http\Resources\Nomenclature;

use App\Models\Election;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ElectionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /* @var Election $this */
        return [
            'id' => $this->id,
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'type' => $this->type->getLabel(),
            'is_live' => $this->is_live,
            'slug' => $this->slug,
            'created_at' => $this->created_at,
        ];
    }
}
