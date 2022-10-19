<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Models\Token;
use Carbon\Carbon;

class TokenHelpers
{
    /**
     * @param string $token
     * @param int $tokenValidTimeMin
     * @return bool
     */
    public function checkToken(string $token, int $tokenValidTimeMin = Token::TOKEN_VALID_TIME_MIN): bool
    {
        $result = false;
        $tokenFoundTable = Token::query()->where('token', '=', $token)->get();
        if(isset($tokenFoundTable[0])) {
            $createdAt = $tokenFoundTable[0]->created_at;
            $currentTime = Carbon::now();
            $currentTime->subMinutes($tokenValidTimeMin);
            if($createdAt > $currentTime) {
                $result = true;
            }
        }
        return $result;
    }

    /**
     * @param string $token
     * @return void
     */
    public function deleteToken(string $token): void
    {
        $tokenFoundTable = Token::query()->where('token', '=', $token)->get();
        if(isset($tokenFoundTable[0])) {
            $tokenFoundTable[0]->delete();
        }
    }
}
