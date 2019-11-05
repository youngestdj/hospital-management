<?php

namespace App\Utils;

use Firebase\JWT\JWT;
use Crisu83\ShortId\ShortId;

class Helpers
{
    /**
     * Get verification key from the database
     * @param string $user Root|Admin|Doctor|Patient
     * @param string $email
     * @return string verification key
     * @phan-file-suppress PhanPartialTypeMismatchArgument
     */
    public static function getVerificationKey($user, $email)
    {
        $model = '\App\Models\\'.$user;
        $verificationKey = $model::where('email', $email)->pluck('verification_key')->first();
        return $verificationKey;
    }

    public static function signToken($userId, $role)
    {
        $token = [
        "iss" => "hospital-management",
        "iat" => time(),
        "exp" => time() + (60*60*24),
        "data" => [
          "user" => $role,
          "userId" => $userId
        ]
        ];
        return JWT::encode($token, \config('auth.jwt_secret'));
    }

    /**
     * Generate verification key
     * @return string
     */
    public static function generateKey()
    {
        $shortid = ShortId::create();
        return $shortid->generate() . $shortid->generate();
    }
}
