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
use Illuminate\Http\Resources\Json\JsonResource;

class NomenclatureController extends Controller
{
    /**
     * @operationId Elections
     */
    public function elections(): JsonResource
    {
        return ElectionResource::collection(
            Election::query()
                ->orderBy('is_live', 'desc')
                ->get()
        );
    }

    /**
     * @operationId Countries
     */
    public function countries(): JsonResource
    {
        return CountryResource::collection(Country::all());
    }

    /**
     * @operationId Counties
     */
    public function counties(): JsonResource
    {
        return CountyResource::collection(County::all());
    }

    /**
     * @operationId County
     */
    public function county(County $county): JsonResource
    {
        return CountyResource::make(
            $county->loadMissing('localities')
        );
    }
}
