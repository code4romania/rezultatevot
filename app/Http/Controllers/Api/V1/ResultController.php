<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Enums\DataLevel;
use App\Http\Controllers\Controller;
use App\Http\Resources\Result\ResultResource;
use App\Models\Record;
use Illuminate\Http\Resources\Json\JsonResource;

class ResultController extends Controller
{
    /**
     * @operationId ResultTotal
     */
    public function total(): JsonResource
    {
        $result= Record::forLevel(
            level: DataLevel::TOTAL,
            aggregate: true,
        )->get();
        dd($result);

        return ResultResource::make();
    }

    /**
     * @operationId ResultDiaspora
     */
    public function diaspora(): JsonResource
    {
        return ResultResource::make();
    }

    /**
     * @operationId ResultCountry
     */
    public function country(): JsonResource
    {
        return ResultResource::make();
    }

    /**
     * @operationId ResultNational
     */
    public function national(): JsonResource
    {
        return ResultResource::make();
    }

    /**
     * @operationId ResultCounty
     */
    public function county(): JsonResource
    {
        return ResultResource::make();
    }
}
