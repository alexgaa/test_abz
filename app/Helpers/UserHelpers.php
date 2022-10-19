<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Models\User;

class UserHelpers
{
    /**
     * @param string $email
     * @param string $phone
     * @return bool
     */
    public function checkForUniqueMailPhone(string $email, string $phone): bool
    {
        $result = true;
        $user = User::query()
            ->orWhere('email', '=', $email)
            ->orWhere('phone', '=', $phone)->get();
        if(isset($user[0]->id)){
            $result = false;
        }
        return $result;
    }
}
