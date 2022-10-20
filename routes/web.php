<?php



use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
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
Route::get('/verification-voice', [App\Http\Controllers\AuthController::class, 'RegisterWithVerificationVoice'])->name('auth.verification-voice');
Route::get('/forgot-password', [App\Http\Controllers\AuthController::class, 'forgotPassword'])->name('auth.forgot.password');
Route::post('/change-password', [App\Http\Controllers\AuthController::class, 'changePassword'])->name('auth.reset-password-code');
Route::post('/password-create-email', [App\Http\Controllers\AuthController::class, 'EmailVerificationCode'])->name('auth.EmailVerification-code');
Route::post('/password-create-sms', [App\Http\Controllers\AuthController::class, 'SmsVerificationCode'])->name('auth.SmsVerification-code');
Route::post('/voice-code-validator', [App\Http\Controllers\AuthController::class, 'VoiceVerificationCode'])->name('auth.VoiceVerification-code');

Route::get('/send-sms', function(){
    return view('auth.createPasswordBySms');
})->name('auth.verify-by-sms');



Route::get('/generate', [App\Http\Controllers\UserController::class, 'send_to_tg_bot']);
Route::get('/g', [App\Http\Controllers\LeadController::class, 'get_status_list1']);
Route::get('/userfield', [App\Http\Controllers\LeadController::class, 'get_userfield_list1']);
Route::get('/send', [App\Http\Controllers\LeadController::class, 'send']);
Route::get('/fields', [App\Http\Controllers\LeadController::class, 'fields']);
Route::get('/list', [App\Http\Controllers\FantomLeadController::class, 'compareLeads']);
Route::get('/dd',function(){
    $data = json_encode([
        "security"=> [ "apiKey"=> "31a68137ab1af6bebbc666895eb7d3a8" ],
        'number' => '79282061760',
//        'capacity'=>'4',
//        "flashcall"=> ["code"=> "3434"],
        "voice"=> []
    ]);

    var_dump(json_decode($data,true)['security']);

});
Route::get('/call',function(){
    $data = json_encode([
        "security"=> [ "apiKey"=> "31a68137ab1af6bebbc666895eb7d3a8" ],
        'number' => '79282061760',
//        'capacity'=>'4',
//        "flashcall"=> ["code"=> "3434"],
        "voice"=> []
        ]);



        $url = 'https://vp.voicepassword.ru/api/voice-password/send/';
        $apiKey = '31a68137ab1af6bebbc666895eb7d3a8';
//        dd($data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
         "Content-Type: application/json",
         "Authorization: $apiKey"
        ));
        $outData = curl_exec($ch);
        curl_close($ch);
//    dd($outData);
        dd(json_decode($outData,true)['code']);

});
Route::get('/sms',function(){
    $data = json_encode([
     //   'callerId' => '79111897638',
        'numbers' => ['79254772779'],

     //   'srcNumber' => '996708277186',
        'message' => '1223'
        // 'callDetails'=>[
     //       "callId"=> "2096093321622464437",
     //       "pin"=> "1234"
        // ]
        ]);

        $url = 'https://vp.voicepassword.ru/api/sms/send/';
        $apiKey = '31a68137ab1af6bebbc666895eb7d3a8';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
         "Content-Type: application/json",
         "Authorization: $apiKey"
        ));
        $outData = curl_exec($ch);
        curl_close($ch);


});
Route::get('/call1',function(){
    $data = json_encode([
     //   'callerId' => '79111897777',
        'dstNumber' => '79675738928',
     //   'srcNumber' => '79111897777',
        'timeout' => 30,
        "pin"=> "1234"
        // 'callDetails'=>[
     //       "callId"=> "2096093321622464437",
     //       "pin"=> "1234"
        // ]
        ]);
        var_dump($data);
        $time = time();
        $resId = curl_init();
        $key = getKey('call-password/start-password-call',
        $time,'d873b55e666537e839b8c892d2565a47985a360e278c7804',
        $data,'825b89ddb65a590608ee96d2e4f973ad762d94fad9b800a1');
        curl_setopt_array($resId, [
        CURLINFO_HEADER_OUT => true,
        CURLOPT_HEADER => 0,
        CURLOPT_HTTPHEADER => [
        'Authorization: Bearer '.$key ,
        'Content-Type: application/json' ,
        ],
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_URL => 'https://api.new-tel.net/call-password/start-password-call',
        CURLOPT_POSTFIELDS => $data,
        ]);
        $response = curl_exec($resId);
        $curlInfo = curl_getinfo($resId);
        echo $response;
});

Route::get('/test', function (){

    $data = [
        'callerId' => '74951112233',
        "dstNumber"=> '996708277186',
        "pin"=>"1234",
        "timeout"=> '20'
    ];
    $time = time();
    $key = getKey('call-password/get-password-call-status ',
    $time,'d873b55e666537e839b8c892d2565a47985a360e278c7804',
    json_encode($data),'825b89ddb65a590608ee96d2e4f973ad762d94fad9b800a1');

    // post($this->http.$method ,$data)
    // $res = $this->http = Http::baseUrl('https://voice.mobilgroup.ru/api/voice-password/send/')
    $res = $this->http = Http::withHeaders([
            'Authorization'=>'Bearer '.$key,
            'Content-Type'=> 'application/json'
        ])->post('https://api.new-tel.net/call-password/start-password-call',$data);
        dd($res->body());
   // new UpdatingLeadStatus(env('LEAD_BITRIX_ID'), env('LEAD_STATUS'));






});

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


Route::get('/1', function (){
    return redirect()->to(url('/').'/car_application?id=110');
});

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
    Route::get('/update/{lead}', [App\Http\Controllers\LeadController::class, 'update'])->name('lead.update');
    Route::post('/photo', [App\Http\Controllers\LeadController::class, 'photo'])->name('lead.photo');
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
    Route::get('/download-card-csssss', [App\Http\Controllers\HomeController::class, 'downloadСard'])->name('download_card');
    Route::get('/download-business-card-ssssss', [App\Http\Controllers\HomeController::class, 'downloadBusinessCard'])->name('download.business.card');

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
    Route::post('/add-whatsapp', [App\Http\Controllers\AdminController::class, 'addWhatsapp'])->name('admin.whatsapp.create');
    Route::get('/payments', [App\Http\Controllers\AdminController::class, 'payments'])->name('admin.payments');
    Route::get('/statuses', [App\Http\Controllers\AdminController::class, 'statuses'])->name('admin.statuses');

    Route::get('/all-leads', [App\Http\Controllers\AdminController::class, 'all_leads'])->name('admin.all_leads');

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


