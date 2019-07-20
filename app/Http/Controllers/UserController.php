<?php

namespace App\Http\Controllers;

use App\Http\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
  private $request;
  function __construct(Request $request)
  {
    $this->request = $request;
  }

  public function addAdmin()
  {
    $validator = Validator::make($this->request->all(), [
      'email' => 'required|email|unique:admins',
      'firstname' => 'required|min:2',
      'lastname' => 'required|min:2'
    ]);
    if ($validator->fails()) {
      return response()->json([
        "success" => "false",
        "errors" => $validator->errors()
      ]);
    }
    $createAdmin = Admin::create($this->request->all());
    if ($createAdmin) {
      return response()->json(["success" => "true", "admin" => $createAdmin->only('id', 'email', 'firstname', 'lastname')]);
    } else return response()->json(["success" => "true"]);
  }
}
