<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDoctorsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('doctors', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->string('email');
      $table->string('firstname');
      $table->string('lastname');
      $table->string('phone');
      $table->string('gender');
      $table->string('dob');
      $table->string('specialization');
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
    Schema::dropIfExists('doctors');
  }
}
