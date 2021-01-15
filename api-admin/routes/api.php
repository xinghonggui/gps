<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


//开放信息分组
Route::prefix('gps')->group(function($router) {
    $router->any('test', 'App\Http\Controllers\IndexController@test');
    $router->any('imeis', 'App\Http\Controllers\IndexController@imeis');
    $router->any('getgpsbyimei', 'App\Http\Controllers\IndexController@getgpsbyimei');
    $router->any('gettrace', 'App\Http\Controllers\IndexController@gettrace');
    $router->any('getgooglegps', 'App\Http\Controllers\IndexController@getgooglegps');
});