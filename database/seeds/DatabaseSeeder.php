<?php

use App\Models\Admin;
use App\Mail\AdminAdded;
use Crisu83\ShortId\ShortId;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Mail;

class DatabaseSeeder extends Seeder
{
  /**
   * Seed the application's database.
   *
   * @return void
   */
  public function run()
  {
    $shortid = ShortId::create();
    $verificationKey = $shortid->generate() . $shortid->generate();
    $createRoot = Admin::firstOrCreate(
      ["email" => \config('mail.root')],
      [
        "firstname" => "Root",
        "lastname" => "User",
        "verification_key" => $verificationKey,
      ]
    );
    if ($createRoot->wasRecentlyCreated) {
      Mail::to(\config('mail.root'))->send(new AdminAdded(["user" => "admin", "key" => $verificationKey]));
    }
  }
}
