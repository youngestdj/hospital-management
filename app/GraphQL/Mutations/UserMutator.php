<?php

namespace App\GraphQL\Mutations;

use App\Exceptions\CustomException;
use App\Utils\Helpers;
use App\Mail\UserAdded;
use App\Mail\AccountVerified;
use Exception;
use Illuminate\Support\Facades\Mail;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class UserMutator
{

    /**
         * Return a value for the field.
         *
         * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
         * @param  mixed[]  $args The arguments that were passed into the field.
         * @return mixed
         * @phan-file-suppress PhanUnusedPublicNoOverrideMethodParameter, PhanUnusedVariableCaughtException
         */
    public function addUser($rootValue, array $args, GraphQLContext $context)
    {
        try {
            $target = $context->request()->get('target');
            $model = 'App\\Models\\'.$target;
            $user = new $model($args);
            $verificationKey = Helpers::generateKey();
            $user->verification_key = $verificationKey;
            $user->save();

            if ($user->wasRecentlyCreated && isset($args['email'])) {
                Mail::to($args['email'])
                ->send(new UserAdded([
                  "user" => $target,
                  "key" => $verificationKey,
                  "firstname" => $user->firstname,
                  "lastname" => $user->lastname
                ]));
            }
            return collect($user)->except(['password', 'created_at', 'updated_at', 'reset_key', 'reset_expires']);
        } catch (Exception $e) {
            throw new CustomException('Could not add user.', 'Something went wrong.');
        }
    }

    /**
     * Return a value for the field.
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  mixed[]  $args The arguments that were passed into the field.
     * @return mixed
     * @phan-file-suppress PhanUnusedPublicNoOverrideMethodParameter
     */
    public function verifyUser($rootValue, array $args)
    {
        try {
            $user = $args['user'];
            $model = 'App\\Models\\'.$user;
            $verificationKey = $model::where('verification_key', $args['key'])->first();
        
            if (count($verificationKey) < 1) {
                return 'Invalid verification key.';
            } else {
                $verificationKey->password = $args['password'];
                $verificationKey->verification_key = "";
                $verificationKey->save();

                if ($verificationKey->wasChanged() && isset($verificationKey->email)) {
                    Mail::to($verificationKey->email)
              ->send(new AccountVerified([
                "user" => $user,
                "firstname" => $verificationKey->firstname,
                "lastname" => $verificationKey->lastname
              ]));
                }
                return 'Account has been verified. You can now log in with your email and password.';
            }
        } catch (Exception $e) {
            throw new CustomException('Could not verify user.', 'Something went wrong.');
        }
    }
}
