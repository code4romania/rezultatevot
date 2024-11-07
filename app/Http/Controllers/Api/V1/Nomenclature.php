<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Nomenclature\ElectionResource;
use App\Models\Election;

class Nomenclature extends Controller
{
    public function elections()
    {
        $elections = Election::query()->orderBy('is_live')->get();

        return response()->json(ElectionResource::collection($elections));
    }
}
