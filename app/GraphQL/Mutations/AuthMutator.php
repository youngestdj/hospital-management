<?php

namespace App\GraphQL\Mutations;

use App\Utils\Helpers;
use Exception;
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
        if (($args['user'] === 'Root') && ($args['email'] !== \config('mail.root'))) {
            throw new CustomException(
                'Invalid email or password.',
                'Login failed.'
            );
        }

        $model = 'App\\Models\\'.$args['user'];
        try {
            $verifyUser = $model::where('email', strtolower($args['email']))->first();
            $verifyPassword = Hash::check($args['password'], $verifyUser['password']);

            if (!$verifyPassword) {
                throw new CustomException(
                    'Invalid email or password.',
                    'Login failed.'
                );
            }

            $verifyUser->token = Helpers::signToken($verifyUser['id'], $args['user']);
            return $verifyUser->only('id', 'email', 'firstname', 'lastname', 'token');
        } catch (Exception $e) {
            throw new CustomException(
                'Invalid email or password.',
                'Login failed.'
            );
        }
    }
}
