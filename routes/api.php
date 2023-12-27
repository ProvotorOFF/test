<?php

use App\Http\Middleware\BearerAuth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware([BearerAuth::class])->prefix('v1')->group(function () {
    Route::match(['get', 'post'], '', function () {
        switch (request()->query('method')) {
            case 'rates':
                return request()->method() == 'GET' ? app()->call('App\Http\Controllers\CurrencyController@rates') : response()->json([
                    "status" => "error",
                    "code" => 400,
                    "message" => "Only GET is supported"
                ], 400);
            case 'convert':
                return request()->method() == 'POST' ? app()->call('App\Http\Controllers\CurrencyController@convert') : response()->json([
                    "status" => "error",
                    "code" => 400,
                    "message" => "Only POST is supported"
                ], 400);
            default:
            return response()->json([
                "status" => "error",
                "code" => 400,
                "message" => "Incorrect method value"
            ], 400);
        }
    });
});
