<?php

namespace App\Http\Controllers;

use App\Auth\Code;
use App\Clients\SmsClient;
use App\Mail\MailUser;
use App\Models\AuthConfirmation;
use App\Models\balance;
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
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    use RegistersUsers;
    protected $redirectTo = RouteServiceProvider::HOME;
    public $fieldType;
    public $email;
    public $code;

    public function forgotPassword(){
        return view('auth.passwords.forgotpassword');
    }
    public function resetPassword(Request $request, Code $code,SmsClient $sms){
        $this->fieldType = filter_var($request['email'] , FILTER_VALIDATE_EMAIL) ? 'email' : 'number';
        $this->email = $request->email ?? '';
        $this->code = $code->generate(CODE::VERIFICATION);

        $param = array();
        ($request->email != '') ?
            $param['email'] = $this->email :
            $param['phone'] = $request->number;
        $param['code'] =$this->code;

        if($this->fieldType=='number'){
            $validated = $request->validate(['number' => 'required|unique:users|phone_number']);
            $sms->sendSms($request->number, "Ваш код: ".$this->code);
            AuthConfirmation::updateOrCreate( $param);
            //$sms->sendSms(+996708277186, "Ваш код: ".$this->code);
            return view('auth.createPasswordSms',$request->input());
        }else{
            $validated = $request->validate(['email' => 'required|unique:users|max:255']);
            AuthConfirmation::updateOrCreate( $param);
            Mail::to($request->email)->send((new MailUser())
                ->markdown('mail.code', ['code' => $this->code,
                    'message' => 'Пожалуйста, введите код для проверки вашего email.',
                    'not' => 'Если вы не создавали аккаунт, не нужно ничего делать.'
                ]));
            return view('auth.createPasswordEmail',$request->input());
        }
    }
    public function RegisterWithVerificationCode(Request $request, Code $code,SmsClient $sms)
    {
        //dd($request->toArray());
        \Session::put('last_auth_attempt', 'register');

        $this->fieldType = filter_var($request['email'] , FILTER_VALIDATE_EMAIL) ? 'email' : 'number';
        $this->email = $request->email ?? '';
        $this->code = $code->generate(CODE::VERIFICATION);

        $param = array();
        ($request->email != '') ?
            $param['email'] = $this->email :
            $param['phone'] = $request->number;
        $param['code'] =$this->code;

        if($this->fieldType=='number'){
            $validated = $request->validate(['number' => 'required|unique:users|phone_number']);
            $sms->sendSms($request->number, "Ваш код: ".$this->code);
            AuthConfirmation::updateOrCreate( $param);
            //$sms->sendSms(+996708277186, "Ваш код: ".$this->code);
            return view('auth.createPasswordSms',$request->input());
        }else{
            $validated = $request->validate(['email' => 'required|unique:users|max:255']);
            AuthConfirmation::updateOrCreate( $param);
            Mail::to($request->email)->send((new MailUser())
                ->markdown('mail.code', ['code' => $this->code,
                    'message' => 'Пожалуйста, введите код для проверки вашего email.',
                    'not' => 'Если вы не создавали аккаунт, не нужно ничего делать.'
                ]));
            return view('auth.createPasswordEmail',$request->input());
        }

    }

    public function SmsVerificationCode(Request $request){
        $this->fieldType='number';
        $validated = Validator::make($request->all(), [
            'number' => ['required','phone_number', 'unique:users'],
            'code' => ['required', 'integer', new CheckEmailVerificationCode()],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [], [
            'password' => 'Пароль'
        ]);

        if ($validated->fails()) {
            return view('auth.createPasswordSms', $request->input())->withInput($request->input())->withErrors($validated);
        }
        event(new Registered($user = $this->createUser($request->all())));
        $this->guard()->login($user);
        return $request->wantsJson()
            ? new JsonResponse([], 201)
            : redirect($this->redirectPath());
    }


    public function EmailVerificationCode(Request $request){

        $validated = Validator::make($request->all(), [
            'email' => ['required', 'string', 'max:255', 'unique:users'],
            'code' => ['required', 'integer', new CheckEmailVerificationCode()],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [], [
            'password' => 'Пароль'
        ]);

        if ($validated->fails()) {
            //dd($request->input());
            return view('auth.createPasswordEmail', $request->input())->withInput($request->input())->withErrors($validated);
        }
        event(new Registered($user = $this->createUser($request->all())));
        $this->guard()->login($user);
        return $request->wantsJson()
            ? new JsonResponse([], 201)
            : redirect($this->redirectPath());

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
//        dump($data);
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
            $reason = Reason::create(['table_id'=>$user->id,'reason_name'=>'refer','user_id_who_referred'=>$source_user->id]);
            Refer::create(['user_id'=>$source_user->id,'referred_user_id'=>$user->id]);

            $p = PaymentAmount::where('reason_of_payment','refer')->first();


            $payment = Payment::create(['user_id'=>$source_user->id,'reason_id'=>$reason->id,'amount'=>$p->id,'status'=>false,'status_group'=>'refer']);

            $balance_referred = balance::where('user_id',$source_user->id)->first();
            $balance_referred->balance = $balance_referred->balance +  $payment->payment_amount()->amount;
            $balance_referred->save();

            PendingAmount::create(['payment_id'=>$payment->id,'status'=>0]);

        }
        Setting::create(['number'=>$user->number,'email'=>$user->email, 'user_id'=>$user->id]);
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
