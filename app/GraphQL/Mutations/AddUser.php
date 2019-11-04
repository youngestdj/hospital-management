<?php

namespace App\GraphQL\Mutations;

use App\Exceptions\CustomException;
use App\Utils\Helpers;
use App\Mail\UserAdded;
use Exception;
use Illuminate\Support\Facades\Mail;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class AddUser
{

    /**
         * Return a value for the field.
         *
         * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
         * @param  mixed[]  $args The arguments that were passed into the field.
         * @return mixed
         * @phan-file-suppress PhanUnusedPublicNoOverrideMethodParameter, PhanUnusedVariableCaughtException
         */
    public function resolve($rootValue, array $args, GraphQLContext $context)
    {
        try {
            $target = $context->request()->get('target');
            $model = 'App\\Models\\'.$target;
            $user = new $model($args);
            $verificationKey = Helpers::generateKey();
            $user->verification_key = $verificationKey;
            $user->save();

            if ($user->wasRecentlyCreated && isset($args['email'])) {
                Mail::to($args['email'])->send(new UserAdded(["user" => $target, "key" => $verificationKey]));
            }
            return collect($user)->except(['password', 'created_at', 'updated_at', 'reset_key', 'reset_expires']);
        } catch (Exception $e) {
            throw new CustomException($e->getMessage(), 'Something went wrong.');
        }
    }
}
