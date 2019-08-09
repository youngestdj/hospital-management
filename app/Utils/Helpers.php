<?php

namespace App\Utils;

class Helpers
{
  /**
   * Transform validation errors into a single array of strings
   * 
   * @return array
   */
  public static function transformValidationErrors($errors)
  {
    $transformed = [];
    foreach ($errors as $field => $messages) {
      foreach ($messages as $value) {
        array_push($transformed, $value);
      }
    }
    return $transformed;
  }

  /**
   * Custom function for returning success messages
   * 
   * @return json object
   */
  public static function returnSuccess($successMessage = null, $data = [], $statusCode)
  {
    $result = array_merge(array_filter(["success" => true, "message" => $successMessage]), $data);
    return response()->json($result, $statusCode);
  }

  /**
   * Custom function for returning error messages
   * 
   * @return json object
   */
  public static function returnError($errorMessages, $statusCode)
  {
    return response()->json([
      "success" => false,
      "errors" => $errorMessages
    ], $statusCode);
  }
}
