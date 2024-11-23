<?php

declare(strict_types=1);

namespace App\Http\Resources\Turnout;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DemographicsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = collect($this->resource)
            ->map(fn ($value) => (int) $value);

        return [
            'men' => [
                '18-24' => (int) $data->get('men_18-24'),
                '25-34' => (int) $data->get('men_25-34'),
                '35-44' => (int) $data->get('men_35-44'),
                '45-64' => (int) $data->get('men_45-64'),
                '65+' => (int) $data->get('men_65'),
            ],

            'women' => [
                '18-24' => (int) $data->get('women_18-24'),
                '25-34' => (int) $data->get('women_25-34'),
                '35-44' => (int) $data->get('women_35-44'),
                '45-64' => (int) $data->get('women_45-64'),
                '65+' => (int) $data->get('women_65'),
            ],
        ];
    }
}
