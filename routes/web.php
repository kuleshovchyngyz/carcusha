<?php


use App\Models\User;
use App\support\Leads\UpdatingLeadStatus;
use Illuminate\Support\Facades\Route;

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

Route::get('/car_application', [App\Http\Controllers\ApplicationController::class, 'qrform'])->name('car_application');
Route::post('/car_application/store', [App\Http\Controllers\ApplicationController::class, 'store'])->name('car.store');
Route::get('/calculate_available_amount', [App\Http\Controllers\AdminController::class, 'calculate_available_amount']);
Route::post('/verification-code', [App\Http\Controllers\AuthController::class, 'RegisterWithVerificationCode'])->name('auth.verification-code');
Route::get('/forgot-password', [App\Http\Controllers\AuthController::class, 'forgotPassword'])->name('auth.forgot.password');
Route::post('/change-password', [App\Http\Controllers\AuthController::class, 'changePassword'])->name('auth.reset-password-code');
Route::post('/password-create-email', [App\Http\Controllers\AuthController::class, 'EmailVerificationCode'])->name('auth.EmailVerification-code');
Route::post('/password-create-sms', [App\Http\Controllers\AuthController::class, 'SmsVerificationCode'])->name('auth.SmsVerification-code');



Route::get('/generate', [App\Http\Controllers\UserController::class, 'send_to_tg_bot']);
Route::get('/g', [App\Http\Controllers\LeadController::class, 'get_status_list1']);
Route::get('/send', [App\Http\Controllers\LeadController::class, 'send']);
Route::get('/fields', [App\Http\Controllers\LeadController::class, 'fields']);

Route::get('/test', function (){
    new UpdatingLeadStatus('78413', 17);
});
Route::get('/delete', function (){
    \App\Models\PendingAmount::latest()->first()->delete();
    \App\Models\Payment::latest()->first()->delete();
    \App\Models\Reason::latest()->first()->delete();
    \App\Models\Notification::latest()->first()->delete();
    \App\Models\MessageNotification::latest()->first()->delete();
});






Route::get('admin/l/{id}', [App\Http\Controllers\AdminController::class, 'pay_to_partner']);



Route::get('/', function () {
    return redirect('/leads');
})->name('home');




Auth::routes();

//Route::get('/leads', [App\Http\Controllers\HomeController::class, 'index'])->name('leads');

Route::get('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('customlogout');

Route::get('/car', [App\Http\Controllers\LeadController::class, 'get_car_models']);
//Route::get('/test/{id}', [App\Http\Controllers\LeadController::class, 'get_status']);
//Route::get('/getlist', [App\Http\Controllers\LeadController::class, 'get_status_list']);
Route::post('/testimage', [App\Http\Controllers\LeadController::class, 'testimage']);
Route::post('/deleteimage', [App\Http\Controllers\LeadController::class, 'deleteimage']);
//Route::get('/list', [App\Http\Controllers\LeadController::class, 'get_some']);
//Route::get('/telega', [App\Http\Controllers\LeadController::class, 'telega'])->name('pull');
//Route::get('/tester', [App\Http\Controllers\LeadController::class, 'tester']);
Route::group(['prefix'=>'leads', 'middleware' => ['auth', 'role:user']], function () {
    Route::get('/', [App\Http\Controllers\LeadController::class, 'index'])->name('lead.list');

    Route::get('/create', [App\Http\Controllers\LeadController::class, 'create'])->name('lead.create');
    Route::post('/store', [App\Http\Controllers\LeadController::class, 'store'])->name('lead.store');
});

Route::group([ 'middleware' => ['auth', 'role:user']], function () {
    //Route::post('/send-verification-code', [App\Http\Controllers\Auth\LoginController::class, 'sendVerificationCode'])->name('auth.send-verification-code');

    Route::get('/notifications', [App\Http\Controllers\HomeController::class, 'notifications'])->name('notification.list');
    Route::get('/payments', [App\Http\Controllers\HomeController::class, 'payments'])->name('payment.list');
    Route::get('/paymentqueries', [App\Http\Controllers\HomeController::class, 'paymentqueries'])->name('payment.paymentqueries');
    Route::get('/refer', [App\Http\Controllers\HomeController::class, 'refer'])->name('refer.list');
    Route::get('/settings', [App\Http\Controllers\HomeController::class, 'settings'])->name('settings');
    Route::get('/promo', [App\Http\Controllers\HomeController::class, 'promo'])->name('promo');
    Route::post('/confirm-email', [App\Http\Controllers\AuthController::class, 'confirmEmail'])->name('confirm.email');
    Route::post('/confirm-number', [App\Http\Controllers\AuthController::class, 'confirmNumber'])->name('confirm.number');
    Route::get('/download_card', [App\Http\Controllers\HomeController::class, 'downloadСard'])->name('download_card');
    Route::get('/download-business-card', [App\Http\Controllers\HomeController::class, 'downloadBusinessCard'])->name('download.business.card');

    Route::post('/settings/edit', [App\Http\Controllers\UserController::class, 'edit_settings'])->name('settings.edit');
    Route::post('/query_for_payment', [App\Http\Controllers\UserController::class, 'query_for_payment'])->name('query_for_payment');
    Route::post('/notification_setting', [App\Http\Controllers\UserController::class, 'notification_setting'])->name('notification_setting');
    Route::post('/promo', [App\Http\Controllers\UserController::class, 'promo'])->name('user.promo');

    Route::get('/help', [App\Http\Controllers\HomeController::class, 'help'])->name('help');
});
Route::group(['prefix'=>'admin', 'middleware' => ['auth', 'role:admin']], function () {
    Route::get('/', [App\Http\Controllers\AdminController::class, 'index'])->name('admin.users');
    Route::get('user/{user}', [App\Http\Controllers\AdminController::class, 'user'])->name('admin.user');
    Route::post('user/paymentype', [App\Http\Controllers\AdminController::class, 'changePaymentType']);
   // Route::get('user/{user}/{type}', [App\Http\Controllers\AdminController::class, 'SortByNumber'])->name('admin.user.sortByNumber');
    Route::get('user/new/{user}', [App\Http\Controllers\AdminController::class, 'newleads'])->name('admin.user.new');
    Route::get('user/verifyleads/{user}', [App\Http\Controllers\AdminController::class, 'verifyleads'])->name('admin.user.verify_leads');

    Route::post('/reset-password-admin', [App\Http\Controllers\AuthController::class, 'adminPasswordReset'])->name('auth.Reset-password');
    Route::get('user/ban/{user}', [App\Http\Controllers\AdminController::class, 'ban'])->name('admin.user.ban');
    Route::get('user/report/{user}', [App\Http\Controllers\AdminController::class, 'report'])->name('admin.user.report');
    Route::get('/settings', [App\Http\Controllers\AdminController::class, 'settings'])->name('admin.settings');
    Route::get('/ads', [App\Http\Controllers\AdminController::class, 'ads'])->name('admin.ads');
    Route::post('/ads/store', [App\Http\Controllers\AdminController::class, 'storeAds'])->name('admin.store.ads');
    Route::get('/payment_settings', [App\Http\Controllers\AdminController::class, 'payments_settings'])->name('admin.payments_settings');
    Route::get('/payments', [App\Http\Controllers\AdminController::class, 'payments'])->name('admin.payments');
    Route::get('/statuses', [App\Http\Controllers\AdminController::class, 'statuses'])->name('admin.statuses');
    Route::post('/store_user_statuses', [App\Http\Controllers\AdminController::class, 'store_user_statuses'])->name('admin.store_user_statuses');
    Route::post('/settings/store', [App\Http\Controllers\AdminController::class, 'store_settings'])->name('admin.store_settings');
    Route::post('/settings/payment_settings', [App\Http\Controllers\AdminController::class, 'store_payment_settings'])->name('admin.store_payment_settings');
    Route::post('/settings/user_payment_settings', [App\Http\Controllers\AdminController::class, 'store_user_payment_settings'])->name('admin.store_user_payment_settings');
    Route::get('/settings/user_payment_settings/{user}', [App\Http\Controllers\AdminController::class, 'user_payment_settings'])->name('admin.user_payment_settings');
    Route::get('/pay/{paid}', [App\Http\Controllers\AdminController::class, 'pay_to_partner']);
});


