<?php

namespace App\Http\Controllers;

use App\Auth\Code;
use App\Clients\SmsClient;
use App\Clients\CallAuth;
use App\Mail\MailUser;
use App\Models\AuthConfirmation;
use App\Models\balance;
use App\Models\Major;
use App\Models\Payment;
use App\Models\PaymentAmount;
use App\Models\PendingAmount;
use App\Models\Reason;
use App\Models\Refer;
use App\Models\Setting;
use App\Models\User;
use App\Notifications\VerifyEmail;
use App\Providers\RouteServiceProvider;
use App\Rules\CheckEmailVerificationCode;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    use RegistersUsers;
    use ResetsPasswords;
    protected $redirectTo = RouteServiceProvider::HOME;
    public $fieldType;
    public $email;
    public $code;

    public function forgotPassword(){
        return view('auth.passwords.forgotpassword');
    }
    public function changePassword(Request $request){
        return $this->RegisterWithVerificationCode($request,'reset');
    }
    public function confirmNumber(Request $request){

        $request->request->add(['confirmPhone' => true]);
        $validated = Validator::make($request->all(), [
            'code' => ['required', 'integer', new CheckEmailVerificationCode()],
        ], [], []);
        if ($validated->fails()) {
            if(Str::contains(Route::currentRouteName(), 'api')){
                return response()->json(['old_values'=>$request->input(),'errors'=>$validated->errors()], 400);
            }
            return view('auth.createPasswordEmail',$request->input())->withInput($request->input())->withErrors($validated);
        }
        $user = auth()->user();
        $user->setting->number = $request->number;
        $user->setting->save();

        $user->phone_verified_at = Carbon::now();
        $user->number = $user->setting->number;
        $user->save();
        if(Str::contains(Route::currentRouteName(), 'api')){
            return response()->json(['success_message'=>'confirmed'], 200);
        }
        $request->request->remove('confirmPhone');
        return redirect()->route('settings');
    }
    public function confirmEmail(Request $request){

        $request->request->add(['confirmEmail' => true]);
        $validated = Validator::make($request->all(), [
            'code' => ['required', 'integer', new CheckEmailVerificationCode()],
        ], [], []);
        if ($validated->fails()) {
            if(Str::contains(Route::currentRouteName(), 'api')){
                return response()->json(['old_values'=>$request->input(),'errors'=>$validated->errors()], 400);
            }
            return view('auth.createPasswordEmail',$request->input())->withInput($request->input())->withErrors($validated);
        }

       $user = auth()->user();
       $user->setting->email = $request->email;
       $user->setting->save();

       $user->email_verified_at = Carbon::now();
       $user->email = $user->setting->email;
       $user->save();
        if(Str::contains(Route::currentRouteName(), 'api')){
            return response()->json(['success_message'=>'confirmed'], 200);
        }
        //$request->flashExcept('code');
        $request->request->remove('confirmEmail');
       return redirect()->route('settings');
    }



    public function RegisterWithVerificationCode(Request $request, $type = '')
    {
        if(Str::contains(Route::currentRouteName(), 'api')){
            (!$request->has('major')) ? $request->request->add(['major' => 'sds']) : "";
        }

        $code = new Code();
        $sms = new SmsClient();
        $call = new CallAuth();
        \Session::put('last_auth_attempt', 'register');
        $this->fieldType = isset($request['email'])  ? 'email' : 'number';

        $this->email = $request->email ?? '';
        $this->code = $code->generate(CODE::VERIFICATION);
        $param = array();
        ($request->email != '') ?
            $param['email'] = $this->email :
            $param['phone'] = $request->number;
        $param['code'] =$this->code;
        ($type=='reset') ? $request->request->add(['reset' => true]) : "";

        if($this->fieldType=='number'){
            try {
                ($request->has('reset')) ?
                    $request->validate(['number' => 'required|phone_number|is_number_in_database']) :
                    $request->validate(['number' => 'not_empty|unique:users|phone_number','major' => 'required_major','invitation_code'=>
                        ($request->invitation_code!==null) ? 'is_promocode_in_database' : '']);
            } catch (\Illuminate\Validation\ValidationException $th) {
                return $th->validator->errors();
            }
             $call->call(preg_replace('/[^0-9]/', '', $request->number),$this->code);
//            $param['code'] = $call->call(preg_replace('/[^0-9]/', '', $request->number));
            AuthConfirmation::updateOrCreate( $param);
           // $sms->sendSms(+996708277186, "Ваш код: ".$this->code);
           if(!$request->has('reset')){
                $id = Major::wherename($request->major)->first()->id;
                $request->merge(['major' => $id]);
            }
            // dd($request->input());

            if(Str::contains(Route::currentRouteName(), 'api')){
                return response()->json([
                                        'next_url'=> \route('api.auth.VoiceVerification-code'),
                                        'nex_method'=>'post',
                                        'expected_inputs'=>'number,major,code,invitation_code',
                                        'createPasswordVoice' => $request->input()],
                    200);
            }

            return view('auth.createPasswordVoice',$request->input());
        }else {

            ($request->has('reset')) ?
                $request->validate(['email' => 'required|email_format|is_email_in_database|max:255']) :
                $request->validate(['major' => 'required_major','email' => 'not_empty|email_format|unique:users|max:255','invitation_code'=>
                    ($request->invitation_code!==null) ? 'is_promocode_in_database' : '']);
//            return response()->json(['createPasswordEmail' => $request->all()],200);
            AuthConfirmation::updateOrCreate( $param);
            Mail::to($request->email)->send((new MailUser())->subject("Регистрация на сайте SKYvin.ru Код:".$this->code)
                ->markdown('mail.code', ['code' => $this->code,
                    'message' => 'Пожалуйста, введите код для проверки вашего email.',
                    'not' => 'Если вы не создавали аккаунт, не нужно ничего делать.'
            ]));

            if(!$request->has('reset')){
                $id = Major::wherename($request->major)->first()->id;
                $request->merge(['major' => $id]);
            }

            if(Str::contains(Route::currentRouteName(), 'api')){
                return response()->json(['createPasswordEmail' => $request->input()],200);
            }

            return view('auth.createPasswordEmail',$request->input());
        }
    }
    public function SmsVerificationCode(Request $request){
        if(Str::contains(Route::currentRouteName(), 'api')){
            $id = Major::wherename($request->major)->first()->id;
            $request->merge(['major' => $id]);
        }
        $this->fieldType = 'number';
        $validated = Validator::make($request->all(), [
            'number' => ['required', 'string', 'max:255', (!isset($request->reset)) ? 'unique:users' : ''],

            'password' => ['required', 'string', 'min:8', 'confirmed'],

        ], [], [
            'password' => 'Пароль'
        ]);

        if ($validated->fails()) {
            if(Str::contains(Route::currentRouteName(), 'api')){
                return response()->json(['old_values'=>$request->input(),'errors'=>$validated->errors()], 400);
            }
            return view('auth.createPasswordVoice', $request->input())->withInput($request->input())->withErrors($validated);
        }
        if(isset($request->reset)){
            return $this->resetUserPassword($request);
        }else{
            return $this->registerUser($request);
        }

    }
    public function SendSms(Request $request, $type = '')
    {
        $code = new Code();
        $sms = new SmsClient();
        $call = new CallAuth();
        $this->email = $request->email ?? '';
        $this->code = $code->generate(CODE::VERIFICATION);
        $param = array();
        ($request->email != '') ?
            $param['email'] = $this->email :
            $param['phone'] = $request->number;
        $param['code'] =$this->code;
        $call->sms(preg_replace('/[^0-9]/', '', $request->number),$this->code);
        AuthConfirmation::updateOrCreate( $param);
        // dump(2324);
        // return 34343;
        return view('auth.createPasswordBySms',$request->input());
    }

    public function VoiceVerificationCode(Request $request){
        if(Str::contains(Route::currentRouteName(), 'api')){
            $id = Major::wherename($request->major)->first()->id;
            $request->merge(['major' => $id]);
        }

        if($request->has('verifyBytel')){

            return $this->SendSms($request);
        }else{
            $this->fieldType = 'number';
            $validated = Validator::make($request->all(), [
                'code' => ['required', 'integer', Str::contains(Route::currentRouteName(), 'api') ? '': new CheckEmailVerificationCode()],
            ], [], [
                'password' => 'Пароль'
            ]);

            if ($validated->fails()) {
                if(Str::contains(Route::currentRouteName(), 'api')){
                    return response()->json(['old_values'=>$request->input(),'errors'=>$validated->errors()], 400);
                }
                return view('auth.createPasswordVoice', $request->input())->withInput($request->input())->withErrors($validated);
            }
            if(isset($request->reset)){
                // return $this->resetUserPassword($request);
                return view('auth.createPasswordSms',$request->input());
            }else{
                if(Str::contains(Route::currentRouteName(), 'api')){
                    return response()->json(['next_url'=>\route('api.auth.SmsVerification-code'),'expected_inputs'=>'number,major,code,invitation_code,password,password_confirmation','old_values'=>$request->input()], 400);
                }
                return view('auth.createPasswordSms',$request->input());
            }
        }


    }
    public function VerificationPassword(Request $request){
        $this->fieldType = 'number';
        $validated = Validator::make($request->all(), [
            'number' => ['required','phone_number',  (!isset($request->reset)) ? 'unique:users' : ''],
            'code' => ['required', 'integer', new CheckEmailVerificationCode()],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [], [
            'password' => 'Пароль'
        ]);

        if ($validated->fails()) {
            return view('auth.createPasswordSms', $request->input())->withInput($request->input())->withErrors($validated);
        }
        if(isset($request->reset)){
            return $this->resetUserPassword($request);
        }else{
            return $this->registerUser($request);
        }

    }
    public function adminPasswordReset(Request $request){
//        return $request->all();
        $validated = Validator::make($request->all(), [
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [], [
            'password' => 'Пароль'
        ]);
        if ($validated->fails()) {
            //return back()->withErrors($validated);
            return response()->json(['errors'=>$validated->errors()->all()]);
        }
        $user = User::find($request->user_id);
        $user->password = Hash::make($request->password);
        $user->save();
        return response()->json(['success'=>'Data is successfully added']);
        //dd($request->all());
    }
    public function registerUser(Request $request){

        event(new Registered($user = $this->createUser($request->all())));
        if(Str::contains(Route::currentRouteName(), 'api')) {
//            return 3443433443;
            if($request->has('email')){
                if ($token = auth()->guard('api')->attempt(['email' => $request->email, 'password' => $request->password])) {
                    return $this->respondWithToken($token);
                }
            }else{
                if ($token = auth()->guard('api')->attempt(['number' => $request->number, 'password' => $request->password])) {
                    return $this->respondWithToken($token);
                }
            }
        }
        $this->guard()->login($user);
        return $request->wantsJson()
            ? new JsonResponse([], 201)
            : redirect($this->redirectPath());
    }
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',

            'expires_in' => auth('api')->factory()->getTTL()
        ]);
    }
    public function resetUserPassword(Request $request){
        AuthConfirmation::where('email',$request->email)->delete();
        if($this->fieldType == 'number'){
            $user = User::where('number',$request->number)->first();
        }else{
            $user = User::where('email',$request->email)->first();
        }

        $this->resetPassword($user ,$request->password);

        if($user->hasRole('admin')){
            return redirect('/admin');
        }
        return $request->wantsJson()
            ? new JsonResponse([], 201)
            : redirect($this->redirectPath());
    }
    public function EmailVerificationCode(Request $request){
        if(Str::contains(Route::currentRouteName(), 'api')){
            $id = Major::wherename($request->major)->first()->id;
            $request->merge(['major' => $id]);
        }
//        dd($request->all());
        $validated = Validator::make($request->all(), [
            'email' => ['required', 'string', 'max:255', (!isset($request->reset)) ? 'unique:users' : ''],
            'code' => ['required', 'integer', new CheckEmailVerificationCode()],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [], [
            'password' => 'Пароль'
        ]);
        if ($validated->fails()) {

            if(Str::contains(Route::currentRouteName(), 'api')){
                return response()->json(['old_values'=>$request->input(),'errors'=>$validated->errors()], 400);
            }
            return view('auth.createPasswordEmail', $request->input())->withInput($request->input())->withErrors($validated);
        }
        if(isset($request->reset)){
            return $this->resetUserPassword($request);
        }else{
            return $this->registerUser($request);
        }

    }

    /**
     * Create a new user instance after a valid registration.
     *
     *
     * @return \App\Models\User
     */
    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function createUser(array $data)
    {
        // dd($data);
        $major = Major::where('id',$data['major'])->first()->id;


//        dd($this->credentials($data));
        $refer = false;
        if(isset($data['invitation_code'])){
            if(trim($data['invitation_code'])!=''){
                if ($this->invitation_codeExists($data['invitation_code'])) {
                    $source_user = User::where('invitation_code',$data['invitation_code'])->first();
                    $refer = true;

                }

            }
        }
        //dd($this->credentials($data));
        $user = User::create($this->credentials($data));
        $user->assignRole('user');
        $balance = balance::create(['user_id'=>$user->id,'balance'=>0]);
        if($refer){
//            $reason = Reason::create(['table_id'=>$user->id,'reason_name'=>'refer','user_id_who_referred'=>$source_user->id]);
            Refer::create(['user_id'=>$source_user->id,'referred_user_id'=>$user->id]);

//            $p = PaymentAmount::where('reason_of_payment','refer')->first();


//            $payment = Payment::create(['user_id'=>$source_user->id,'reason_id'=>$reason->id,'amount'=>$p->amount,'status'=>false,'status_group'=>'refer']);

//            $balance_referred = balance::where('user_id',$source_user->id)->first();
//            $balance_referred->balance = $balance_referred->balance +  $payment->amount;
//            $balance_referred->save();
//            PendingAmount::create(['payment_id'=>$payment->id,'status'=>0]);
        }
        Setting::create(['number'=>$user->number,'email'=>$user->email, 'user_id'=>$user->id,'major_id'=>$major]);

        return $user;
    }
    protected function generateinvitation_code() {
        $users = User::count()==0 ? 1 : User::count();
        $number = mt_rand(1, 9999*($users%1000));
        $numlength = strlen(intval(strrev($number)));
        $number = intval((strrev($number))*(100000/(pow(10,$numlength))));
        $number = (strrev((string)$number));

        if ($this->invitation_codeExists($number)) {
            return $this->generateinvitation_code();
        }
        return $number;
    }

    function invitation_codeExists($number) {
        return User::whereinvitation_code($number)->exists();
    }

    protected function credentials(array $data)
    {
        AuthConfirmation::where('code',$data['code'])->delete();
        $info = array();
        if($this->fieldType=='number'){
            $info = ['number'=>$data['number'],'invitation_code'=> $this->generateinvitation_code(),'password'=>Hash::make($data['password']),'phone_verified_at'=>Carbon::now()];
            return $info;
        }
        elseif (filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $info = ['email' => $data['email'],'invitation_code'=> $this->generateinvitation_code(), 'password'=>Hash::make($data['password']),'email_verified_at'=>Carbon::now()];
            return $info;
        }
        return [];
    }

    /**
     * Get the guard to be used during registration.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard();
    }
}
