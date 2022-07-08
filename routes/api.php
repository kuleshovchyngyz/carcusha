<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JWTController;
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



//Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login'])->middleware('throttle:10,1')->name('api.login');
Route::get('/notifications', [App\Http\Controllers\HomeController::class, 'notifications'])->middleware('throttle:10,1')->name('api.notification.list');
Route::post('/check-promo', [App\Http\Controllers\ApplicationController::class, 'checkPromo'])->name('api.checkPromo');
Route::post('/password-create-email', [App\Http\Controllers\AuthController::class, 'EmailVerificationCode'])->name('api.auth.EmailVerification-code');
Route::post('/password-create-sms', [App\Http\Controllers\AuthController::class, 'SmsVerificationCode'])->name('api.auth.SmsVerification-code');
//Route::post('/register', [JWTController::class, 'register']);
Route::get('/register', [JWTController::class, 'showRegisterForm']);
Route::post('/login', [JWTController::class, 'login']);
Route::post('/verification-code', [App\Http\Controllers\AuthController::class, 'RegisterWithVerificationCode'])->name('api.auth.verification-code');
Route::post('/voice-code-validator', [App\Http\Controllers\AuthController::class, 'VoiceVerificationCode'])->name('api.auth.VoiceVerification-code');
//Route::post('/login', [JWTController::class, 'login']);

Route::group(['middleware' => 'jwt.verify'], function($router) {
    Route::get('/leads', [App\Http\Controllers\LeadController::class, 'index'])->middleware(['middleware' => 'api'])->name('api.leads');
    Route::post('/logout', [JWTController::class, 'logout']);
    Route::post('/refresh', [JWTController::class, 'refresh']);
    Route::post('/profile', [JWTController::class, 'profile']);

});
