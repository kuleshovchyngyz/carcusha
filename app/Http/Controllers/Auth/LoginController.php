<?php

namespace App\Http\Controllers\Auth;

use App\Auth\Code;
use App\Clients\SmsClient;
use App\Http\Controllers\Controller;
use App\Models\AuthConfirmation;
use App\Models\User;
use App\Notifications\VerifyEmail;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    protected $fieldType;

    /**
     * Where to redirect users after login.
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
        $this->middleware('guest')->except('logout');

    }
    protected function validateLogin(Request $request)
    {
        \Session::put('last_auth_attempt', 'login');
//        dd($request->all());
//        $fieldType = filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'number';
        $this->fieldType = isset($request['email']) ? 'email' : 'number';
        if($this->fieldType=='number'){
            $request->request->add(['number' => $request->number]);
            $request->request->remove('email');
        }

        $request->validate([
            $this->fieldType => 'required|string',
            'password' => 'required|string',
        ]);
    }


    public function username()
    {
        return $this->fieldType;
    }
    protected function authenticated(Request $request, $user)
    {
        if($user->hasRole('admin')){
            return redirect('/admin');
        }
        if($user->active==0){
            return redirect('/logout')->with('error_message', ['Вы заблокированы']);
        }
    }

    public function sendVerificationCode(Request $request, Code $code, SmsClient $sms)
    {
        $this->email = $request->email ?? '';
        $this->code = $code->generate(CODE::VERIFICATION);
        if($request->email != ''){
            $this->notify(new VerifyEmail());
        } else {
            $sms->sendSms('+'.preg_replace('/[^0-9]/', '', $request->phone), "Ваш код: ".$this->code);
            //$sms->sendSms($request->phone, "Ваш код: ".$this->code);
        }
        $param = ($request->email != '') ? ['email' => $this->email] : ['phone' => $request->phone];
        AuthConfirmation::updateOrCreate(
            $param,
            ['code' => $this->code]
        );

        return redirect()->route('auth.confirm-code', ['email'=>$request->email ?? '', 'phone'=>$request->phone ?? '']);
    }

}
