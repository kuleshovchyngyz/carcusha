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
Route::get('/download-card-csssss/{id}', [App\Http\Controllers\ApiSettings::class, 'downloadСard'])->name('api.download_card');
Route::get('/download-business-card-ssssss/{id}', [App\Http\Controllers\ApiSettings::class, 'downloadBusinessCard'])->name('api.download.business.card');
//Route::post('/login', [JWTController::class, 'login']);

Route::group(['middleware' => 'jwt.verify'], function($router) {
    Route::get('/leads', [App\Http\Controllers\LeadController::class, 'index'])->middleware(['middleware' => 'api'])->name('api.leads');
    Route::post('/logout', [JWTController::class, 'logout']);
    Route::post('/refresh', [JWTController::class, 'refresh']);
    Route::post('/profile', [JWTController::class, 'profile']);
    Route::get('/notifications', [App\Http\Controllers\HomeController::class, 'notifications'])->middleware(['middleware' => 'api'])->name('api.notification.list');
    Route::get('/available-balance', [App\Http\Controllers\HomeController::class, 'available_balance'])->middleware(['middleware' => 'api'])->name('api.available_balance');
    Route::get('/notifications/seen', [App\Http\Controllers\HomeController::class, 'seen'])->middleware(['middleware' => 'api'])->name('api.notification.seen');
    Route::get('/paymentqueries', [App\Http\Controllers\HomeController::class, 'paymentqueries'])->name('api.payment.paymentqueries');
    Route::post('/query_for_payment', [App\Http\Controllers\UserController::class, 'query_for_payment'])->name('api.query_for_payment');
    Route::get('/payments', [App\Http\Controllers\HomeController::class, 'payments'])->name('api.payment.list');
    Route::get('/refer', [App\Http\Controllers\HomeController::class, 'refer'])->name('api.refer.list');
    Route::get('/promo', [App\Http\Controllers\HomeController::class, 'promo'])->name('api.promo');
    Route::post('/promo', [App\Http\Controllers\UserController::class, 'promo'])->name('api.user.promo');
    Route::get('/settings', [App\Http\Controllers\HomeController::class, 'settings'])->name('api.settings');
    Route::post('/settings/confirm-promo', [App\Http\Controllers\ApiSettings::class, 'edit_api_settings_confirmPromo']);
    Route::post('/settings/confirm-email', [App\Http\Controllers\ApiSettings::class, 'edit_api_settings_confirmEmail'])->name('api.sendCodeToEmail');
    Route::post('/settings/confirm-number', [App\Http\Controllers\ApiSettings::class, 'edit_api_settings_confirmNumber'])->name('api.sendCodeToPhone');
    Route::post('/settings/edit', [App\Http\Controllers\ApiSettings::class, 'edit_settings'])->name('api.settings.edit');
    Route::post('/confirm-email-code', [App\Http\Controllers\AuthController::class, 'confirmEmail'])->name('api.confirm.email');
    Route::post('/confirm-number-code', [App\Http\Controllers\AuthController::class, 'confirmNumber'])->name('api.confirm.number');
    Route::get('/help', [App\Http\Controllers\HomeController::class, 'help'])->name('api.help');
    Route::get('/updates', [App\Http\Controllers\HomeController::class, 'updates'])->name('api.updates');

});
