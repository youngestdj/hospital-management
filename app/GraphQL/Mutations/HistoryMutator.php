<?php

namespace App\GraphQL\Mutations;

use Exception;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Exceptions\CustomException;
use App\Mail\HistoryAdded;
use Illuminate\Support\Facades\Mail;

class HistoryMutator
{
    /**
     * Return a value for the field.
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  mixed[]  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @phan-file-suppress PhanUnusedPublicNoOverrideMethodParameter, PhanUnusedVariableCaughtException
     * @return mixed
     */
    public function addHistory($rootValue, array $args, GraphQLContext $context)
    {
        try {
            $history = new \App\Models\History($args);
            $history->doctor_id = $context->request()->get('userId');
            $history->save();

            if ($history->wasRecentlyCreated && isset($history->patient->email)) {
                Mail::to($history->patient->email)->send(new HistoryAdded(
                    [
                      "prescription" => $history->prescription
                    ],
                    [
                      "firstname" => $history->patient->firstname,
                      "lastname" => $history->patient->lastname
                    ]
                ));
            }
            return $history;
        } catch (Exception $e) {
            throw new CustomException('Could not add history.', 'Something went wrong.');
        }
    }
}
