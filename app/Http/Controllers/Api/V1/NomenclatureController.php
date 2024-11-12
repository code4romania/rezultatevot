<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Nomenclature\CountryResource;
use App\Http\Resources\Nomenclature\CountyResource;
use App\Http\Resources\Nomenclature\ElectionResource;
use App\Models\Country;
use App\Models\County;
use App\Models\Election;
use Illuminate\Http\JsonResponse;

class NomenclatureController extends Controller
{
    public function elections(): JsonResponse
    {
        return response()->json(
            ElectionResource::collection(
                Election::query()
                    ->orderBy('is_live', 'desc')
                    ->get()
            )
        );
    }

    public function counties(): JsonResponse
    {
        return response()->json(
            CountyResource::collection(
                County::with(['localities' => function ($query) {
                    $query->whereNull('parent_id');
                }])->get()
            )
        );
    }

    public function county(County $county): JsonResponse
    {
        return response()->json(
            new CountyResource(
                $county->load('localities')
            )
        );
    }

    public function countries(): JsonResponse
    {
        return response()->json(
            CountryResource::collection(
                Country::all()
            )
        );
    }
}
