<?php

namespace App\GraphQL\Mutations;

use App\Utils\Helpers;
use Exception;
use Illuminate\Support\Arr;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Hash;

class AuthMutator
{
    /**
     * Return a value for the field.
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  mixed[]  $args The arguments that were passed into the field.
     * @return mixed
     * @phan-file-suppress PhanUnusedPublicNoOverrideMethodParameter, PhanUnusedVariableCaughtException
     */
    public function login($rootValue, array $args)
    {
        $credentials = Arr::only($args, ['email', 'password', 'user']);
        $model = 'App\\Models\\'.$credentials['user'];
        try {
            $verifyUser = $model::where('email', strtolower($credentials['email']))->first();
            $verifyPassword = Hash::check($credentials['password'], $verifyUser['password']);

            if (!$verifyPassword) {
                throw new CustomException(
                    'Invalid email or password.',
                    'Login failed.'
                );
            }

            $verifyUser->token = Helpers::signToken($verifyUser['id'], $credentials['user']);
            return $verifyUser->only('id', 'email', 'firstname', 'lastname', 'token');
        } catch (Exception $e) {
            throw new CustomException(
                'Invalid email or password.',
                'Login failed.'
            );
        }
    }
}
