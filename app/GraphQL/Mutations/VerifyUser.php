<?php

namespace App\GraphQL\Mutations;

class VerifyUser
{
    /**
     * Return a value for the field.
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  mixed[]  $args The arguments that were passed into the field.
     * @return mixed
     * @phan-file-suppress PhanUnusedPublicNoOverrideMethodParameter
     */
    public function resolve($rootValue, array $args)
    {
        $user = $args['user'];
        $model = 'App\\Models\\'.$user;
        $verificationKey = $model::where('verification_key', $args['key'])->first();
        
        if (count($verificationKey) < 1) {
            return 'Invalid verification key.';
        } else {
            $verificationKey->password = $args['password'];
            $verificationKey->verification_key = "";
            $verificationKey->save();
            return 'Account has been verified. You can now log in with your email and password.';
        }
    }
}
