<?php

namespace App\Http\Controllers;

use App\Models\Cars;
use App\Models\Lead;
use App\Models\Major;
use App\Models\MessageNotification;
use App\Models\Paid;
use App\Models\Payment;
use App\Models\PaymentAmount;
use App\Models\Promo;
use App\Models\Question;
use App\Models\Status;
use App\Models\Notification;
use App\support\QrCode\QRCodeGenerator;
use Fpdf\Fpdf;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use setasign\Fpdi\Fpdi;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
//        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }
    public function notifications()
    {
		//dd(888);
        $messages = MessageNotification::where('user_id', auth()->user()->id)->get();
        if(Str::contains(Route::currentRouteName(), 'api')){
            return response()->json(['notifications'=>$messages->toArray()], 200);
        }
        return view('home',[
            'name' => 'notifications',
            'data' => $messages
        ]);

    }
    public function payments(){
        $leads = Lead::select('*')
            ->where('user_id',Auth::user()->id)
			 ->orderBy('updated_at','DESC')
            ->get();

        $payments = [];
        if(Str::contains(Route::currentRouteName(), 'api')){
            foreach (auth()->user()->lead_payments() as $key =>$payment){
                $payments[$key]['date'] = $payment->created_at->format('d-m-Y');
                $payments[$key]['vendor'] = $payment->reasons->lead()->vendor;
                $payments[$key]['vendor_model'] = $payment->reasons->lead()->vendor_model;
                $payments[$key]['vendor_year'] = $payment->reasons->lead()->vendor_year;
                $payments[$key]['amount'] = $payment->amount;
            }
            return response()->json(['payments'=>$payments], 200);
        }
        return view('home',[
            'name' => 'payments',
            'data' => $leads
        ]);
    }
    public function paymentqueries(){
        $paid = Paid::where('user_id',\auth()->user()->id)->get();
        //dd($leads);
        if(Str::contains(Route::currentRouteName(), 'api')){
            return response()->json(['paymentqueries'=>$paid->toArray()], 200);
        }
        return view('home',[
            'name' => 'paymentqueries',
            'data' => $paid
        ]);
    }
    public function refer(){
        $partners = array_flip(\auth()->user()->partners()->pluck('id')->toArray());
        foreach ($partners as $key =>$partner){
            $partners[$key] = 0;
        }
        $payments = Payment::where('status_group','refer')->where('user_id',\auth()->user()->id)->get();
        foreach ($payments as $payment){
            $val = isset($partners[$payment->reasons->table_id]) ? $partners[$payment->reasons->table_id] : 0;
            $partners[$payment->reasons->table_id] = $payment->amount +  $val;
        }

        $refer=[];
        if(Str::contains(Route::currentRouteName(), 'api')){
            $refer['referral_link'] =\route('login').'?ref='.Auth::user()->invitation_code;
            $refer['promocode'] = Auth::user()->invitation_code;
            $refer['total_number_of_partners'] = Auth::user()->partners()->count();
            $refer['number_of_partner_leads'] = Auth::user()->number_of_partner_leads();
            $refer['total_money_amount_from_referral'] = Auth::user()->total_amount_from_referral();
            foreach( Auth::user()->partners() as $partner){
                $refer['partners'][$partner->id]['phone']=$partner->setting->number;
                $refer['partners'][$partner->id]['email']=$partner->setting->email;
                $refer['partners'][$partner->id]['total_number_of_leads']=$partner->numberOfLeads();
                $refer['partners'][$partner->id]['pending_leads']=$partner->pending();
                $refer['partners'][$partner->id]['successful_leads']=$partner->successful() ;
                $refer['partners'][$partner->id]['rejected_leads']=$partner->rejected();
                $refer['partners'][$partner->id]['funded_money']=$partners[$partner->id];
            }
            return response()->json($refer, 200);
        }

        return view('home',[
            'name' => 'refer',
            'data' => $partners
        ]);

    }
    public function settings(){
        $settings=[];
        $settings['email']='';
        $settings['number']='';
        if(Str::contains(Route::currentRouteName(), 'api')){
            if( \ViewService::init()->view('number') !== null){
                $settings['number']=\ViewService::init()->view('number');
            }
            if(\ViewService::init()->view('email')!==null){
                $settings['email']=\ViewService::init()->view('email');
            }
            $settings['city']=auth()->user()->setting->city;
            $settings['invitationCode']=\ViewService::init()->view('InvitationCode');
            $settings['major']=\ViewService::init()->view('majors');
            $majors= Major::select(['id', 'name'])->get()->toArray();
            $paymentSettings=( auth()->user()->paymentSetting==null ) ? [] : auth()->user()->paymentSetting->makeHidden(['id','user_id','created_at','updated_at'])->toArray() ;
            $telegramLink =  app('App\Http\Controllers\UserController')->telegramNotification();
            $settings['saving_data_url']=\route('api.settings.edit');
            return response()->json([
                'input_data'=>array_merge($settings,$paymentSettings),
                'majors'=>$majors,
                'telegramLink'=>$telegramLink,
                'confirm_email_post_method_url'=>\route('api.sendCodeToEmail'),
                'confirm_number_post_method_url'=>\route('api.sendCodeToPhone')
            ], 200);
        }
        return view('home',[
            'name' => 'settings',
            'data' => ''
        ]);
    }


    public function promo(){

        $user = Auth::user();
        $user_id = $user->id;

        if($user->promo===null){
            Promo::firstOrCreate([
                'user_id'=>$user->id,
                'name'=>'',
                'phone'=>$user->setting->number,
                'email'=>$user->setting->email,
                'address'=>'',
                'generated'=>false
            ]);
        }

//        $QRPics = new QRCodeGenerator(route('car_application').'?id='.$user_id);
        $QRPics = new QRCodeGenerator(route('promo'));
        $QRPics->touchPromo(Auth::user());
        if(!file_exists(public_path('/qrcodes/plakat_'.Auth::user()->id.'.pdf'))){
            $QRPics->pdf_part_two();
        }

        $promo = [];
        if(Str::contains(Route::currentRouteName(), 'api')){
            $promo['number'] = \ViewService::init()->view('promo_number');

            if(Auth::user()->promo===null){
                $promo['email'] = auth()->user()->setting->email;
                $promo['address'] = '';
                $promo['company'] = '';
            }else {
                $promo['company'] = auth()->user()->promo->name;
                $promo['email'] = auth()->user()->promo->email;
                $promo['address'] = auth()->user()->promo->address;
            }
            if(auth()->user()->promo!==null && auth()->user()->promo->generated){
                $promo['vizitki']['image_link1']=asset('qrcodes/card1.jpg') ;
                $promo['vizitki']['image_link2']=asset('qrcodes/card_qrsmall_'.auth()->user()->id.'.png') ;
                $promo['vizitki']['download_link']=route('download.business.card');
                $promo['plakaty']['image_link']=asset('qrcodes/card_qr_'.auth()->user()->id.'.png');
                $promo['plakaty']['download_link']=route('download.business.card');
            }
            return response()->json($promo, 200);
        }

        return view('home',[
            'name' => 'promo',
            'data' => ''
        ]);
    }
    public function downloadСard(){

        $file = public_path('/qrcodes/plakat_'.Auth::user()->id.'.pdf');
        return Response::download($file);
    }
    public function downloadBusinessCard(){
        $file = public_path('/qrcodes/vizitka_'.Auth::user()->id.'.pdf');
      //  dd($file);
//        dd(4564);
        return Response::download($file);
    }


    public function help(){
        if(Str::contains(Route::currentRouteName(), 'api')){
            $questions = \App\Models\Question::select(['id','question','answer'])->get()->toArray();
            return response()->json($questions, 200);
        }
        return view('home',[
            'name' => 'help',
            'data' => ''
        ]);
    }
    public function updates(){
        if(Str::contains(Route::currentRouteName(), 'api')){

            $updates = \App\Models\Updates::select(['id','version','changes'])->orderBy('created_at', 'DESC')->get();
            return response()->json($updates, 200);
        }
        return view('home',[
            'name' => 'updates',
            'data' => ''
        ]);
    }
}
