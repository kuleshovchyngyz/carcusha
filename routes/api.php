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
//http://partner.kuleshov.studio/api/getdeletedleads
Route::post('/leadupdate', [App\Http\Controllers\LeadController::class, 'lead_status'])->middleware('throttle:10,1')->name('api');
Route::post('/getstatuses', [App\Http\Controllers\LeadController::class, 'getstatuses'])->middleware('throttle:10,1')->name('apiforpartners');
Route::post('/getdeletedleads', [App\Http\Controllers\AdminController::class, 'getDeletedLeads'])->middleware('throttle:10,1')->name('apigetdeletedleads');
Route::post('/telegram', [App\Http\Controllers\UserController::class, 'registerTuser'])->middleware('throttle:10,1')->name('apiforpartnerstelegram');
Route::post('/updateuserfields', [App\Http\Controllers\LeadController::class, 'updateuserfields'])->middleware('throttle:10,1');
