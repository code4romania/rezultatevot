<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Enums\DataLevel;
use App\Http\Controllers\Controller;
use App\Http\Resources\TurnoutResource;
use App\Models\Election;
use App\Models\Turnout;
use Illuminate\Http\JsonResponse;

class TurnoutController extends Controller
{
    public function general(Election $election): JsonResponse
    {
        $result = Turnout::query()
            ->whereBelongsTo($election)
            ->forLevel(
                level: DataLevel::TOTAL,
            )
            ->toBase()
            ->first();

        return response()->json(TurnoutResource::make($result));
    }
}
