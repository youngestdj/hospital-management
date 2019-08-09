<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePatientsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('patients', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->string('email')->nullable();
      $table->string('phone');
      $table->string('firstname');
      $table->string('lastname');
      $table->string('dob');
      $table->string('gender');
      $table->string('occupation');
      $table->string('address');
      $table->string('password')->nullable();
      $table->string('verification_key')->nullable();
      $table->string('expires')->nullable();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('patients');
  }
}
