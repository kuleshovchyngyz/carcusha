<?php

namespace App\Http\Controllers;

use App\Models\Notifier;
use App\Models\Paid;
use App\Models\PaymentAmount;
use App\Models\Setting;
use App\Models\User;
use App\support\QrCode\QRCodeGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use setasign\Fpdi\Fpdi;

class UserController extends Controller
{
public $user_id;
    public function promo(Request $request){
        $QRPics = new QRCodeGenerator(null);
        $QRPics->setCompany(trim($request->company) ?? '');
        $QRPics->setNumber(trim($request->number) ?? '');
        $QRPics->setEmail(trim($request->email) ?? '');
        $QRPics->setAddress(trim($request->address) ?? '');
        $QRPics->pdf_preview();

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

    public function edit_settings(Request $request){
       // dd($request->all());
        if ($request->has('email_notification'))
        {
            Auth::user()->setting->update(['email_notification'=>1]);
        }else{
            Auth::user()->setting->update(['email_notification'=>0]);
        }
        if ($request->has('number_notification'))
        {
            Auth::user()->setting->update(['number_notification'=>1]);
        }else{
            Auth::user()->setting->update(['number_notification'=>0]);
        }
        $request->request->remove('_token');
        $request->request->remove('number_notification');
        $request->request->remove('email_notification');
       // Setting::where('user_id',Auth::user()->id)->update($request->all());
        Auth::user()->setting->update($request->all());


        return redirect()->back()->with('success_message', [__('Обновлено')]);
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
