<?php

namespace App\GraphQL\Mutations;

use Illuminate\Support\Arr;
use App\Models\Root;
class VerifyRoot
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
        $credentials = Arr::only($args, ['key', 'password']);
        $verificationKey = Root::where('verification_key', $credentials['key'])->first();
        
        if (count($verificationKey) < 1) {
            return 'Invalid verification key.';
        } else {
            $verificationKey->password = $credentials['password'];
            $verificationKey->verification_key = "";
            $verificationKey->save();
            return 'Root User has been verified. You can now log in with your email and password.';
        }
    }
}
