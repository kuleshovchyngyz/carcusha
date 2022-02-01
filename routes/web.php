<?php


use App\Models\User;
use App\support\Bitrix\ApiConnect;
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

Route::get('/public-offer', [App\Http\Controllers\ApplicationController::class, 'publicOffer'])->name('public_offer');
Route::post('/check-promo', [App\Http\Controllers\ApplicationController::class, 'checkPromo']);
Route::get('/car_application', [App\Http\Controllers\ApplicationController::class, 'qrform'])->name('car_application');
Route::get('/thanks', [App\Http\Controllers\ApplicationController::class, 'thanks']);
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
Route::get('/list', [App\Http\Controllers\FantomLeadController::class, 'compareLeads']);
Route::get('/test', function (){

    // $dealData = new ApiConnect('crm.lead.list', ['order' => ['STATUS_ID'=> 'ASC' ] , 'select'=> [ "ID", "TITLE", "STATUS_ID", "OPPORTUNITY", "CURRENCY_ID" ]]);
    // dd($dealData);
    // dd(sendDataToBitrix1('crm.lead.list', ['order' => ['STATUS_ID'=> 'ASC' ] , 'select'=> [ "ID", "TITLE", "STATUS_ID", "OPPORTUNITY", "CURRENCY_ID" ]]));
    // $imgExt = new Imagick();
    // $imgExt->readImage(public_path('qrcodes').'/vizitka94.pdf');
    // $imgExt->writeImages(public_path().'/pdf_image_doc.jpg', true);

    dd("Document has been converted");
   // new UpdatingLeadStatus(env('LEAD_BITRIX_ID'), env('LEAD_STATUS'));
});
 function sendDataToBitrix1($method, $data) {
    //$webhook_url = "https://b24-85lwia.bitrix24.ru/rest/1/s52ljoksktlyj1ed/";//test
    $webhook_url = "https://carcusha.bitrix24.ru/rest/1/rrr2v2vfxxbw2jeo/";//real
    $queryUrl = $webhook_url . $method ;
    $queryData = http_build_query($data);

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_POST => 1,
        CURLOPT_HEADER => 0,
        CURLOPT_HTTPHEADER => array("Content-Type:multipart/form-data"),
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => $queryUrl,
        CURLOPT_POSTFIELDS => $queryData,

    ));

    $result = curl_exec($curl);
    curl_close($curl);
    return json_decode($result, 1);
}
Route::get('/delete', function (){
    // \App\Models\PendingAmount::latest()->first()->delete();
    // \App\Models\Payment::latest()->first()->delete();
    // \App\Models\Reason::latest()->first()->delete();
    // \App\Models\Notification::latest()->first()->delete();
    // \App\Models\MessageNotification::latest()->first()->delete();
});






Route::get('admin/l/{id}', [App\Http\Controllers\AdminController::class, 'pay_to_partner']);



Route::get('/', function () {
    return redirect('/leads');
})->name('home');




Auth::routes();

//Route::get('/leads', [App\Http\Controllers\HomeController::class, 'index'])->name('leads');

Route::get('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('customlogout');

Route::get('/car', [\App\support\Leads\DropDown::class, 'get_car_models']);
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
    Route::get('/settings/telegramNotification', [App\Http\Controllers\UserController::class, 'telegramNotification'])->name('settings.telegramNotification');
    Route::post('/query_for_payment', [App\Http\Controllers\UserController::class, 'query_for_payment'])->name('query_for_payment');
    Route::post('/notification_setting', [App\Http\Controllers\UserController::class, 'notification_setting'])->name('notification_setting');
    Route::post('/promo', [App\Http\Controllers\UserController::class, 'promo'])->name('user.promo');

    Route::get('/help', [App\Http\Controllers\HomeController::class, 'help'])->name('help');
    Route::get('/updates', [App\Http\Controllers\HomeController::class, 'updates'])->name('updates');
});
Route::group(['prefix'=>'admin', 'middleware' => ['auth', 'role:admin']], function () {
    Route::get('/', [App\Http\Controllers\AdminController::class, 'index'])->name('admin.users');
    Route::get('user/{user}', [App\Http\Controllers\AdminController::class, 'user'])->name('admin.user');
    Route::post('user/paymentype', [App\Http\Controllers\AdminController::class, 'changePaymentType']);
   // Route::get('user/{user}/{type}', [App\Http\Controllers\AdminController::class, 'SortByNumber'])->name('admin.user.sortByNumber');
    Route::get('user/new/{user}', [App\Http\Controllers\AdminController::class, 'newleads'])->name('admin.user.new');
    Route::get('user/verifyleads/{user}', [App\Http\Controllers\AdminController::class, 'verifyleads'])->name('admin.user.verify_leads');

    Route::post('/reset-password-admin', [App\Http\Controllers\AuthController::class, 'adminPasswordReset'])->name('auth.Reset-password');
    Route::post('user/ban/{user}', [App\Http\Controllers\AdminController::class, 'ban'])->name('admin.user.ban');
    Route::get('user/unban/{user}', [App\Http\Controllers\AdminController::class, 'unban'])->name('admin.user.unban');
    Route::post('user/report/{user}', [App\Http\Controllers\AdminController::class, 'report'])->name('admin.user.report');
    Route::get('/settings', [App\Http\Controllers\AdminController::class, 'settings'])->name('admin.settings');
    Route::get('/ads', [App\Http\Controllers\AdminController::class, 'ads'])->name('admin.ads');
    Route::get('/public-offers', [App\Http\Controllers\AdminController::class, 'publicOffers'])->name('admin.offer');
    Route::get('/updates', [App\Http\Controllers\AdminController::class, 'updates'])->name('admin.updates');
    Route::get('/majors', [App\Http\Controllers\AdminController::class, 'majors'])->name('admin.majors');
    Route::post('/public-offers-store', [App\Http\Controllers\AdminController::class, 'storePublicOffers'])->name('admin.store.offers');
    Route::post('/ads/store', [App\Http\Controllers\AdminController::class, 'storeAds'])->name('admin.store.ads');
    Route::post('/majors/store', [App\Http\Controllers\AdminController::class, 'storeMajors'])->name('admin.store.majors');
    Route::get('/payment_settings', [App\Http\Controllers\AdminController::class, 'payments_settings'])->name('admin.payments_settings');
    Route::post('/add-bot', [App\Http\Controllers\AdminController::class, 'addBot'])->name('admin.bot.create');
    Route::get('/payments', [App\Http\Controllers\AdminController::class, 'payments'])->name('admin.payments');
    Route::get('/statuses', [App\Http\Controllers\AdminController::class, 'statuses'])->name('admin.statuses');

    Route::get('/fantoms', [App\Http\Controllers\FantomLeadController::class, 'fantoms'])->name('admin.fantoms');
    Route::get('/fantoms/back-to-bitrix/{lead}', [App\Http\Controllers\FantomLeadController::class, 'backToBirix'])->name('admin.fantom.back.bitrix');
    Route::get('/fantoms/close/{lead}', [App\Http\Controllers\FantomLeadController::class, 'close'])->name('admin.fantom.close');
    Route::get('/fantoms/delete/{lead}', [App\Http\Controllers\FantomLeadController::class, 'delete'])->name('admin.fantom.delete');

    Route::post('/store_user_statuses', [App\Http\Controllers\AdminController::class, 'store_user_statuses'])->name('admin.store_user_statuses');
    Route::post('/settings/store', [App\Http\Controllers\AdminController::class, 'store_settings'])->name('admin.store_settings');
    Route::post('/updates/store', [App\Http\Controllers\AdminController::class, 'store_updates'])->name('admin.store_updates');
    Route::post('/settings/payment_settings', [App\Http\Controllers\AdminController::class, 'store_payment_settings'])->name('admin.store_payment_settings');
    Route::post('/settings/user_payment_settings', [App\Http\Controllers\AdminController::class, 'store_user_payment_settings'])->name('admin.store_user_payment_settings');
    Route::get('/settings/user_payment_settings/{user}', [App\Http\Controllers\AdminController::class, 'user_payment_settings'])->name('admin.user_payment_settings');
    Route::get('/pay/{paid}', [App\Http\Controllers\AdminController::class, 'pay_to_partner']);
});


