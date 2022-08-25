<?php

namespace App\Http\Controllers;
use Illuminate\Foundation\Auth\ResetsPasswords;
use App\Auth\Code;
use App\Clients\CallAuth;
use App\Mail\MailUser;
use App\Models\AuthConfirmation;
use App\Models\Major;
use App\Models\User;
use App\Rules\CheckEmailVerificationCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class ApiAuthController extends Controller
{
    use ResetsPasswords;
    public $code;
    public function __construct()
    {
        $code = new Code();
        $this->code = $code->generate(CODE::VERIFICATION);
    }

    public function sendEmailCode(Request $request){
        $request->has('change_password') ?
            $request->validate(['email' => 'required|email_format|is_email_in_database|max:255']) :
            $request->validate(['email' => 'required|email_format']) ;

        $param = array();
        $param['email'] = $request->email;
        $param['code'] = $this->code;
        AuthConfirmation::updateOrCreate($param);
        Mail::to($request->email)->send((new MailUser())->subject("Регистрация на сайте SKYvin.ru Код:" . $this->code)
            ->markdown('mail.code', ['code' => $this->code,
                'message' => 'Пожалуйста, введите код для проверки вашего email.',
                'not' => 'Если вы не создавали аккаунт, не нужно ничего делать.'
            ]));
        return response()->noContent();
    }
    public function confirmEmailCode(Request $request){

        $validated = Validator::make($request->all(), [
            'email' => [''],
            'code' => ['required', 'integer', new CheckEmailVerificationCode()],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [], [
            'password' => 'Пароль'
        ]);

        if ($validated->fails()) {
            return response()->json(['old_values' => $request->input(), 'errors' => $validated->errors()], 422);
        }

        AuthConfirmation::where('code', $request->code)->delete();
        $user = User::where('email', $request->email)->first();

        $this->resetPassword($user, $request->password);

        JWTAuth::factory()->setTTL(1440000);
        $data = collect($validated->validated())->forget('code')->toArray();
        if (!$token = auth()->guard('api')->attempt($data)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
        ]);
    }
    public function conNumberCode(Request $request){

        $validated = Validator::make($request->all(), [
            'number' => [''],
            'code' => ['required', 'integer', new CheckEmailVerificationCode()],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [], [
            'password' => 'Пароль'
        ]);

        if ($validated->fails()) {
            return response()->json(['old_values' => $request->input(), 'errors' => $validated->errors()], 422);
        }
        AuthConfirmation::where('code', $request->code)->delete();
        $user = User::where('email', $request->email)->first();
        $this->resetPassword($user, $request->password);

        JWTAuth::factory()->setTTL(1440000);
        $data = collect($validated->validated())->forget('code')->toArray();
        if (!$token = auth()->guard('api')->attempt($data)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
        ]);
    }
    public function ResetByNumber(Request $request){
        $request->validate(['number' => 'required|phone_number|is_number_in_database']);

        $sms = new SmsClient();
        $call = new CallAuth();
        $param = array();
        $param['phone'] = $request->number;
        $param['code'] = $this->code;
        //$call->call(preg_replace('/[^0-9]/', '', $request->number),$this->code);//Needs to change
        //$param['code'] = $call->call(preg_replace('/[^0-9]/', '', $request->number));
        AuthConfirmation::updateOrCreate($param);
        return response()->json([
            'next_url' => \route('api.auth.VoiceVerification-code'),
        ],
            200);
        // $sms->sendSms(+996708277186, "Ваш код: ".$this->code);

    }
    public function ResetByEmail(Request $request){
        $request->validate(['email' => 'required|email_format|is_email_in_database|max:255']) ;
        $param = array();
        $param['email'] = $request->email;
        $param['code'] = $this->code;
        AuthConfirmation::updateOrCreate($param);
        Mail::to($request->email)->send((new MailUser())->subject("Регистрация на сайте SKYvin.ru Код:" . $this->code)
            ->markdown('mail.code', ['code' => $this->code,
                'message' => 'Пожалуйста, введите код для проверки вашего email.',
                'not' => 'Если вы не создавали аккаунт, не нужно ничего делать.'
            ]));

        return response()->json(['createPasswordEmail' => $request->input()], 200);


    }

}
