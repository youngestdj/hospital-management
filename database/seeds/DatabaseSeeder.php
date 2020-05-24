<?php

use App\Models\Root;
use App\Mail\UserAdded;
use App\Utils\Helpers;
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
        $verificationKey = Helpers::generateKey();
        $createRoot = Root::firstOrCreate(
            ["email" => \config('mail.root')],
            [
                "verification_key" => $verificationKey,
            ]
        );
        if ($createRoot->wasRecentlyCreated) {
            Mail::to(\config('mail.root'))->send(new UserAdded(["user" => "root", "key" => $verificationKey, "firstname" => "Root", "lastname" => "User"]));
        }
    }
}
