<?php

namespace App\Utils;

class Helpers
{
    /**
     * Transform validation errors into a single array of strings
     *
     * @return array
     */
    // public static function transformValidationErrors($errors)
    // {
    //     $transformed = [];
    //     foreach ($errors as $field => $messages) {
    //         foreach ($messages as $value) {
    //             array_push($transformed, $value);
    //         }
    //     }
    //     return $transformed;
    // }

    /**
     * Custom function for returning success messages
     *
     * @return \Illuminate\Http\JsonResponse object
     */
    public static function returnSuccess($statusCode, $successMessage = null, $data = [])
    {
        $result = array_merge(array_filter(["success" => true, "message" => $successMessage]), $data);
        return response()->json($result, $statusCode);
    }

    /**
     * Custom function for returning error messages
     *
     * @return \Illuminate\Http\JsonResponse object
     */
    public static function returnError($errorMessages, $statusCode)
    {
        return response()->json([
      "success" => false,
      "errors" => $errorMessages
    ], $statusCode);
    }

    /**
     * Get verification key from the database
     * @param string $user Root|Admin|Doctor|Patient
     * @param string $email
     * @return string verification key
     */
    public static function getVerificationKey($user, $email)
    {
        $model = '\App\Models\\'.$user;
        $verificationKey = $model::where('email', $email)->pluck('verification_key')->first();
        return $verificationKey;
    }
}
