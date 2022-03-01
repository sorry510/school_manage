<?php

use App\Mail\TeacherInvitationMail;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Hash;

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

Artisan::command('pwd {password}', function ($password) {
    $hash = Hash::make($password);
    echo $hash;
    $result = Hash::check($password, $hash);
    dd($result);
})->describe('password');

// Artisan::command('chat', function () {
//     $httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient(config('line-bot.channel_access_token'));
//     $bot = new \LINE\LINEBot($httpClient, ['channelSecret' => config('line-bot.channel_secret')]);
//     $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder('hello');
//     $response = $bot->pushMessage('<to>', $textMessageBuilder);
//     echo $response->getHTTPStatus() . ' ' . $response->getRawBody();
// })->describe('chat');
