<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

Route::get('/', function () {
    return 'hello';
    // return view('welcome');
});

Route::get('/login/line', 'Login\LoginController@lineLogin');
Route::get('/login/line/callback', 'Login\LoginController@lineCallback');

Route::get('/swagger', '\L5Swagger\Http\Controllers\SwaggerController@api');

// Route::get('/login', function () {
//     return view('welcome');
// })->name('login');

// Route::get('/login2', function () {
//     return view('welcome');
// })->name('register');

// Route::get('/redirect', function (Request $request) {
//     $request->session()->put('state', $state = Str::random(40));

//     $query = http_build_query([
//         'client_id' => '3',
//         'redirect_uri' => 'http://127.0.0.1:8000/auth/callback',
//         'response_type' => 'code',
//         'scope' => '',
//         'state' => $state,
//     ]);

//     return redirect('http://127.0.0.1:8000/oauth/authorize?' . $query);
// });

// Route::get('/auth/password', function (\Illuminate\Http\Request $request) {
//     $url = config('app.url');
//     $http = new \GuzzleHttp\Client();
//     $response = $http->post("{$url}/oauth/token", [
//         'form_params' => [
//             'grant_type' => 'password',
//             'client_id' => '4',
//             'client_secret' => '8s7NsHGW2H1L03rygmaqqBNaJX2ANKzGW9HJcmAu',
//             'username' => 'test@xueyuanjun.com',
//             'password' => 'test123456',
//             'scope' => '',
//         ],
//     ]);
//     return $url;

//     // return json_decode((string) $response->getBody(), true);
// });
