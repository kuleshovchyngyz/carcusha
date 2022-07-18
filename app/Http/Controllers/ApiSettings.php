<?php

namespace App\Http\Controllers;

use App\Auth\Code;
use App\Clients\CallAuth;
use App\Mail\MailUser;
use App\Models\AuthConfirmation;
use App\Models\Major;
use App\Models\PaymentSetting;
use App\Models\Refer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ApiSettings extends Controller
{

    public function edit_settings(Request $request){
        $major = null;
        if($request->major!==null){
            $major = Major::wherename($request->major)->first()->id;
        }
        \auth()->user()->setting
            ->update([
                'number_notification'=> $request->has('number_notification') ? 1 : 0,
                'email_notification'=> $request->has('email_notification') ? 1 : 0,
                'major_id'=> $major,
                'number'=>$request['number'],
                'email'=>$request['email'],
                'city'=>$request['city']
            ]);
        $request->request->add(['user_id' => auth()->user()->id]);
        $request->request->remove('number_notification');
        $request->request->remove('email_notification');
        $request->request->remove('number');
        $request->request->remove('email');
        $request->request->remove('city');

        // Setting::where('user_id',Auth::user()->id)->update($request->all());

        auth()->user()->paymentSetting==null ?
            PaymentSetting::create($request->all()):
            auth()->user()->paymentSetting->update($request->all());
        return response()->json(['success_message'=>'Обновлено'], 200);
    }
    public function edit_api_settings_confirmPromo(Request $request){
        $user = User::where('invitation_code',$request->invitationCode)->where('id','!=',auth()->user()->id)->first();
        if($user){
            Refer::create(['user_id'=>$user->id,'referred_user_id'=>Auth::user()->id]);
            return response()->json(['success_message'=>'Активировано'], 200);
        }
        return response()->json(['error_message'=>'Нельзя использовать свой промокод'], 406);
    }
    public function edit_api_settings_confirmNumber(Request $request){
        $request->request->add(['confirmPhone' => true]);
        $validated = Validator::make($request->all(), [
            'number' => ['phone_number','confirm_email_settings'],
        ], [], []);
        if ($validated->fails()) {
            if ($validated->fails()) {
                return response()->json(['old_values'=>$request->input(),'errors'=>$validated->errors()], 400);
            }
            // return view('auth.createPasswordSms', $request->input())->withInput($request->input())->withErrors($validated);
        }

//            $param['code'] = $call->call('+'.preg_replace('/[^0-9]/', '', $request->number));
//        $param['code'] = $call->call(preg_replace('/[^0-9]/', '', $request->number));
        //$sms->sendSms(+996708277186, "Ваш код: ".$this->code);
//            AuthConfirmation::updateOrCreate( $param);
        return response()->json(['next_url'=> route('api.confirm.email'),'expected_inputs'=>'code'], 200);
    }
    public function edit_api_settings_confirmEmail(Request $request){
        $user = \auth()->user();
        $code = new Code();
        $sms = new SmsClient();
        $call = new CallAuth();
        $this->code = $code->generate(CODE::VERIFICATION);
        $param = array();
        $param['email'] = $request->email ;
        $param['code'] =$this->code;
        $request->request->add(['confirmEmail' => true]);
        //$request->validate(['email' => 'required|email_format|is_email_in_database|max:255']);
        $validated = Validator::make($request->all(), [
            'email' => ['email_format','required','confirm_email_settings'],
        ], [], []);
        if ($validated->fails()) {
            return response()->json(['old_values'=>$request->input(),'errors'=>$validated->errors()], 400);
        }

        Mail::to($request->email)->send((new MailUser())->subject("Регистрация на сайте SKYvin.ru Код:".$this->code)
            ->markdown('mail.code', ['code' => $this->code,
                'message' => 'Пожалуйста, введите код для проверки вашего email.',
                'not' => 'Если вы не создавали аккаунт, не нужно ничего делать.'
            ]));
        AuthConfirmation::updateOrCreate( $param);
//        route('confirm.email')

    }
}
