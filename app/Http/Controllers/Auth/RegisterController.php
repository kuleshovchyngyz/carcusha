<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\balance;
use App\Models\Payment;
use App\Models\PaymentAmount;
use App\Models\PendingAmount;
use App\Models\Reason;
use App\Models\Refer;
use App\Models\Setting;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    protected $fieldType;
    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        \Session::put('last_auth_attempt', 'register');
        //dd($data);
        $this->fieldType = filter_var($data['email'] , FILTER_VALIDATE_EMAIL) ? 'email' : 'number';
       // dd($this->fieldType);
        if($this->fieldType=='number'){
            $data['number'] = $data['email'];
            unset($data['email']);
        }
        $ru = ['number'=>'Телефон', 'email'=>'E-mail'];
        if($this->fieldType=='number'){
            return Validator::make($data, [
                'number' => ['required','phone_number', 'unique:users'],
                'password' => ['required', 'string', 'min:8']

            ], [], [
                $this->fieldType => $ru[$this->fieldType],
                'password' => 'Пароль'

            ]);
        }else{
            return Validator::make($data, [
                'email' => ['required', 'string', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:8']

            ], [], [
                $this->fieldType => $ru[$this->fieldType],
                'password' => 'Пароль'

            ]);
        }

    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
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
        $user = User::create($this->credentials($data));
        $user->assignRole('user');
        $balance = balance::create(['user_id'=>$user->id,'balance'=>0]);
        if($refer){
                $reason = Reason::create(['table_id'=>$user->id,'reason_name'=>'refer','user_id_who_referred'=>$source_user->id]);
                Refer::create(['user_id'=>$source_user->id,'referred_user_id'=>$user->id]);

                $p = PaymentAmount::where('reason_of_payment','refer')->first();

                $payment = Payment::create(['user_id'=>$source_user->id,'reason_id'=>$reason->id,'amount'=>$p->id,'status'=>false,'status_group'=>'refer']);

                $balance_referred = balance::where('user_id',$source_user->id)->first();
                $balance_referred->balance = $balance_referred->balance +  $p->amount;
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
        //dd($data);

        $info = array();
        $invitation_code = $data['invitation_code'];

        //$fieldType = filter_var($data['email'] , FILTER_VALIDATE_EMAIL) ? 'email' : 'number';

        if($this->fieldType=='number'){
            $info = ['number'=>$data['email'],'invitation_code'=> $this->generateinvitation_code(),'password'=>Hash::make($data['password'])];
            return $info;
        }
        elseif (filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $info = ['email' => $data['email'],'invitation_code'=> $this->generateinvitation_code(), 'password'=>Hash::make($data['password'])];
            return $info;
        }
        //return ['username' => $request->get('email'), 'password'=>$request->get('password')];
    }

}
