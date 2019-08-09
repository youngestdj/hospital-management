<?php

namespace App\Http\Controllers;

use App\Http\Models\Admin;
use App\Http\Models\Doctor;
use App\Http\Models\Patient;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Crisu83\ShortId\ShortId;
use App\Http\Requests\AddAdminRequest;
use Mockery\Exception;

class UserController extends Controller
{
  private $request, $model;
  function __construct(Request $request)
  {
    $this->request = $request;
    $this->model = 'App\Http\Models\\' . $request->user;
  }
  /**
   * Create a new admin
   * 
   * @return json object
   */
  public function addAdmin(AddAdminRequest $request)
  {
    try {
      $validated = $request->validated();
      $shortid = ShortId::create();
      $verificationKey = $shortid->generate() . $shortid->generate();
      // $this->request->request->add(["verification_key" => $verificationKey]);
      $createAdmin = Admin::Create($validated + ["verification_key" => $verificationKey]);

      if ($createAdmin) {
        return response()->json([
          "success" => "true",
          "admin" => $createAdmin->only('id', 'email', 'firstname', 'lastname')
        ], 201);
      } else return response()->json([
        "success" => "false",
        "errors" => "Could not create admin."
      ], 503);
    } catch (Exception $e) {
      return $e;
    }
  }

  /**
   * Verify a user
   * 
   * @return json object
   */
  public function verifyUser()
  {
    $key = $this->model::where('verification_key', $this->request->key)->count();
    if ($key < 1) {
      return response()->json([
        "success" => false,
        "errors" => ["Invalid verification key."]
      ]);
    }

    $validator = Validator::make($this->request->all(), [
      'password' => 'required|min:6'
    ]);
    if ($validator->fails()) {
      return response()->json([
        "success" => false,
        "errors" => $validator->messages()->all()
      ]);
    }

    $verifyUser = $this->model::where('verification_key', $this->request->key)
      ->update(['password' => Hash::make($this->request->password)]);
    if ($verifyUser) {
      return response()->json([
        "success" => true,
        "message" => "Account has been verified"
      ]);
    } else {
      return response()->json([
        "success" => false,
        "errors" => "Could not verify account"
      ]);
    }
  }
}
