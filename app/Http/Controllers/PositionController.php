<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Position;
use Illuminate\Http\JsonResponse;

class PositionController extends Controller
{
    const ERROR_POSITION_NOT_FOUND = [
        "success" => false,
        "message" => "Positions not found",
    ];

    /**
     * @return JsonResponse
     */
    public function getAll(): JsonResponse
    {
        $success = true;
        $positions = Position::all(['id','name']);
        if($positions) {
            return response()->json(compact('success', 'positions'), 200);
        } else {
            return response()->json(self::ERROR_POSITION_NOT_FOUND, 422);
        }
    }
}
