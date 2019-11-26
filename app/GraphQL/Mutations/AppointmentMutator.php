<?php

namespace App\GraphQL\Mutations;

use App\Mail\AppointmentBooked;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Illuminate\Support\Facades\Mail;
use App\Exceptions\CustomException;
use App\Mail\AppointmentApproved;
use App\Mail\PatientAssigned;
use App\Models\Appointment;
use Exception;

class AppointmentMutator
{
    /**
     * Return a value for the field.
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  mixed[]  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @phan-file-suppress PhanUnusedPublicNoOverrideMethodParameter, PhanUnusedVariableCaughtException
     * @return mixed
     */
    public function bookAppointment($rootValue, array $args, GraphQLContext $context)
    {
        try {
            $appointment = new \App\Models\Appointment($args);
            $appointment->patient_id = $context->request()->get('userId');
            $appointment->save();

            if ($appointment->wasRecentlyCreated && isset($appointment->patient->email)) {
                Mail::to($appointment->patient->email)->send(new AppointmentBooked(
                    [
                      "firstname" => $appointment->patient->firstname,
                      "lastname" => $appointment->patient->lastname
                    ]
                ));
            }
            return $appointment;
        } catch (Exception $e) {
            throw new CustomException('Could not book appointment.', 'Something went wrong.');
        }
    }

    /**
     * Return a value for the field.
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  mixed[]  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @return mixed
     */
    public function editAppointment($rootValue, array $args, GraphQLContext $context)
    {
        try {
            $appointment = Appointment::where('id', $args['id'])->first();
            $appointment->doctor_id = $args['doctor_id'];
            array_key_exists("date", $args) and $appointment->date = $args['date'];

            if ($appointment->save() && isset($appointment->patient->email)) {
                $payLoad = [
                  "firstname" => $appointment->patient->firstname,
                  "lastname" => $appointment->patient->lastname,
                ];
                array_key_exists("date", $args) and $payLoad['date'] = $args['date'];
                Mail::to($appointment->patient->email)->send(new AppointmentApproved($payLoad));
                Mail::to($appointment->doctor->email)
                ->send(new PatientAssigned($payLoad, [
                  'firstname' => $appointment->doctor->firstname,
                  'lastname' => $appointment->doctor->lastname
                ], $appointment->date));
            }
            return $appointment;
        } catch (Exception $e) {
            throw new CustomException('Could not edit appointment.', 'Something went wrong.');
        }
    }
}
