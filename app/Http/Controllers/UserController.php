<?php

namespace App\Http\Controllers;
use App\Models\Major;
use GuzzleHttp\Stream\Stream;
use App\Auth\Code;
use App\Clients\CallAuth;
use App\Clients\SmsClient;
use App\Mail\MailUser;
use App\Models\AuthConfirmation;
use App\Models\Notifier;
use App\Models\Paid;
use App\Models\PaymentAmount;
use App\Models\PaymentSetting;
use App\Models\Refer;
use App\Models\Setting;
use App\Models\SiteSetting;
use App\Models\User;
use App\support\QrCode\QRCodeGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use setasign\Fpdi\Fpdi;

class UserController extends Controller
{
    public $user_id;
    public function promo(Request $request){
        $user = Auth::user();
        $user_id = $user->id;
        $QRPics = new QRCodeGenerator(route('car_application').'?id='.$user_id);
//        $QRPics = new QRCodeGenerator(null);
        $QRPics->setCompany(trim($request->company) ?? '');
        $QRPics->setNumber(trim($request->number) ?? '');
        $QRPics->setEmail(trim($request->email) ?? '');
        $QRPics->setAddress(trim($request->address) ?? '');
        $QRPics->pdf_preview();
        return redirect()->back()->with('success_message', [__('Сгенерировано')]);

//        dd($QRPics->big_name);

//        if($request->has('submitplakat')){
//            $file = public_path('/qrcodes/plakat'.Auth::user()->id.'.pdf');
//            return Response::download($file);
//        }
//        if($request->has('submitVizitki')){
//            $file = public_path('/qrcodes/vizitka'.Auth::user()->id.'.pdf');
////        dd(4564);
//            return Response::download($file);
//        }

    }
    public function notification_setting(Request $request){

        $bought = false;
        $on_work = false;
        if(isset($request->bought)){
            $bought = true;
        }
        if(isset($request->on_work)){
            $on_work = true;
        }
        if(\auth()->user()->notifier==null){
            Notifier::create([
                'user_id'=>\auth()->user()->id,
                'on_work' => $on_work,
                'bought' => $bought
            ]);
        }else{
            $n = \auth()->user()->notifier;
            $n->on_work = $on_work;
            $n->bought = $bought;
            $n->save();
        }
        return redirect()->back()->with('success_message', [__('Сохранено')]);
    }
    public function query_for_payment(Request $request){
        $min_amount = PaymentAmount::where('reason_of_payment','MinAmountOfPayment')->first()->amount;

        if( $request->payment_amount > (Auth::user()->availableAmount())){
            return redirect()->back()->with('error_message', [__('Сумма вывода не может быть больше чем на балансе')]);
        }
        if($request->payment_amount < $min_amount ){
            return redirect()->back()->with('error_message', [__('Меньше чем минимальная сумма('.$min_amount.' руб)'.' для вывода')]);

        }else{
            Setting::where('user_id',\auth()->user()->id)->update(['card_number'=>$request->bankcardnumber]);
            Paid::create(['user_id'=>\auth()->user()->id,'status'=>'pending','amount'=>$request->payment_amount,'cardnumber'=>$request->bankcardnumber]);
            return redirect()->back()->with('success_message', [__('Заказано')]);
        }
    }
    public function telegramNotification(){
        $s = SiteSetting::where('name','telegramBotToken')->first();
        $data = ["companycode" => $s->value, "webhook" => route('apiforpartnerstelegram')];
       // dd($s);

        $res = Http::post(env('TELEGRAM_WEB_HOOK_LINK'),$data);

        $username = $res->object()->username;
        $username = str_replace('@','',$username);
        $str = $s->value.'['.\auth()->user()->id.']';
        return Redirect::to('https://t.me/'.$username.'?start='.base64_encode($str));
    }

    public function edit_settings(Request $request){

        if($request->has('confirmPromo')){

            $user = User::where('invitation_code',$request->invitationCode)->where('id','!=',Auth::user()->id)->first();

            if($user){
                Refer::create(['user_id'=>$user->id,'referred_user_id'=>Auth::user()->id]);
                return redirect()->back()->with('success_message',['Активировано']);
            }
            return redirect()->back()->with('error_message',['Нельзя использовать свой промокод']);
        }

       // dd($request->all());
        $user = \auth()->user();
        $code = new Code();
        $sms = new SmsClient();
        $call = new CallAuth();
        $this->code = $code->generate(CODE::VERIFICATION);
        $param = array();
        ($request->has('confirmEmail')) ?
            $param['email'] = $request->email :
            $param['phone'] = $request->number;
        $param['code'] =$this->code;
        $validated = Validator::make($request->all(), [
            'email' => ($request->email!=null) ? ['email_format','confirm_email_settings'] : '',
            'number' => ($request->number!=null) ?  ['phone_number','confirm_phone_settings'] : '',
        ], [], []);


        if ($validated->fails()) {
            $request->flash();
            return view('home',[
                'name' => 'settings',
                'data' =>''
            ])->withInput($request->input())->withErrors($validated);
        }

        if($request->has('confirmEmail') && !$request->has('submitSettings')){
            $request->request->add(['confirmEmail' => true]);
                     //$request->validate(['email' => 'required|email_format|is_email_in_database|max:255']);
            $validated = Validator::make($request->all(), [
                'email' => ['email_format','required','confirm_email_settings'],
            ], [], []);
            if ($validated->fails()) {
                $request->flash();
                return view('home',[
                    'name' => 'settings',
                    'data' =>''
                ])->withInput($request->input())->withErrors($validated);
                // return view('auth.createPasswordSms', $request->input())->withInput($request->input())->withErrors($validated);
            }

            Mail::to($request->email)->send((new MailUser())->subject("Регистрация на сайте SKYvin.ru Код:".$this->code)
                    ->markdown('mail.code', ['code' => $this->code,
                        'message' => 'Пожалуйста, введите код для проверки вашего email.',
                        'not' => 'Если вы не создавали аккаунт, не нужно ничего делать.'
                    ]));
                AuthConfirmation::updateOrCreate( $param);

                return view('auth.createPasswordEmail',$request->input());



        }else if($request->has('confirmPhone') && !$request->has('submitSettings')){
            $request->request->add(['confirmPhone' => true]);
            $validated = Validator::make($request->all(), [
                'number' => ['phone_number','confirm_email_settings'],
            ], [], []);
            if ($validated->fails()) {
                $request->flash();
                return view('home',[
                    'name' => 'settings',
                    'data' =>''
                ])->withInput($request->input())->withErrors($validated);
                // return view('auth.createPasswordSms', $request->input())->withInput($request->input())->withErrors($validated);
            }

//            $param['code'] = $call->call('+'.preg_replace('/[^0-9]/', '', $request->number));
            $param['code'] = $call->call(preg_replace('/[^0-9]/', '', $request->number));
            //$sms->sendSms(+996708277186, "Ваш код: ".$this->code);
//            AuthConfirmation::updateOrCreate( $param);
            return view('auth.createPasswordVoice',$request->input());
        }


        $this->updateSettings($request);

//        session(['success_message' => ['Обновлено']]);
        foreach (old() as $key=>$o){
            $request->session()->forget($key);
        }

        $request->flash();
        //\Session::pull()->all();
        \Session::flash('success_message', ['Обновлено']);

        return view('home',[
            'name' => 'settings',
            'data' =>''

        ]);

    }
    public function updateSettings(Request $request){
        $major = null;
        if($request->major!==null){
            $major = Major::wherename($request->major)->first()->id;
        }
        Auth::user()->setting
            ->update([
                'number_notification'=> $request->has('number_notification') ? 1 : 0,
                'email_notification'=> $request->has('email_notification') ? 1 : 0,
                'major_id'=> $major,
                'number'=>$request['number'],
                'email'=>$request['email'],
                'city'=>$request['city']
            ]);
        $request->request->add(['user_id' => Auth::user()->id]);
        $request->request->remove('_token');
        $request->request->remove('number_notification');
        $request->request->remove('email_notification');
        $request->request->remove('number');
        $request->request->remove('email');
        $request->request->remove('city');

        // Setting::where('user_id',Auth::user()->id)->update($request->all());

        Auth::user()->paymentSetting==null ?
            PaymentSetting::create($request->all()):
            Auth::user()->paymentSetting->update($request->all());
    }

    public function registerTuser(Request $request){
        $response = ['status' => 'error'];
        if ($request->isJson()) {
            $response = ['status' => 'success'];
            $data = $request->all();
            $telegramUserId = $data['telegramUserId'];
            $userId = $data["userId"];
            \Storage::append('responses.txt', time());
            \Storage::append('responses.txt', json_encode($request->all()));

            $userSetting = User::find($userId)->setting;
            $userSetting->telegram_id = $telegramUserId;
            $userSetting->save();

            echo response()->json($response);
        }
        echo response()->json($response);
    }









    public function send_to_tg_bot(){


        $from = 'Partners';

        $message = 'sadfasdfsadfasdfas';



        //companycode - Индивидуальный код организации (получить у администратора)
        $data = ["companycode" => "co19a1ddfa37041", "data" => [["message" => $message]]];
        $url = 'https://t.kuleshov.studio/api/getmessages';
        $data_string = json_encode($data);

        $ch = curl_init($url);
       // curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);

        curl_close($ch);

        return true;
    }
}
