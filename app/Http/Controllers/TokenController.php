<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Token;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class TokenController extends Controller
{

    /**
     * @param int $tokenValidTimeMin
     * @param int $tokenLength
     * @return JsonResponse
     */
    public function get(int $tokenValidTimeMin = Token::TOKEN_VALID_TIME_MIN, int $tokenLength = 250): JsonResponse
    {
        $token = Str::random($tokenLength);
        $currentTime = Carbon::now();
        $currentTime->subMinutes($tokenValidTimeMin);
        $validTime = $currentTime->toDateTimeString();
        $tokenFoundTable = Token::query()->where('created_at','<', $validTime)->limit(1)->get();

        if(isset($tokenFoundTable[0]->id)){
            $tokenTable = Token::query()->find($tokenFoundTable[0]->id);
        } else {
            $tokenTable = new Token();
        }
        $tokenTable->token = $token;
        $newTime = Carbon::now();
        $tokenTable->created_at = $newTime->toDateTimeString();
        $tokenTable->save();

        $success = true;
        return response()->json(compact('success','token'), 200);
    }

}
