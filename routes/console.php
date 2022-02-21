<?php

use App\Mail\TeacherInvitationMail;
use Illuminate\Foundation\Inspiring;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
 */

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->describe('Display an inspiring quote');

Artisan::command('test', function () {
    $user = \App\User::find(1);
    // Creating a token without scopes...
    $token = $user->createToken('token')->accessToken;
    echo $token;
})->describe('test');

Artisan::command('mail', function () {
    \Mail::to('491559675@qq.com')->send(new TeacherInvitationMail());
})->describe('mail test');
