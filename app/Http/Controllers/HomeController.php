<?php

namespace App\Http\Controllers;

use App\Models\Cars;
use App\Models\Lead;
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
            foreach (auth()->user()->lead_payments() as $payment){
                $payments[$payment->id]['date'] = $payment->created_at->format('d-m-Y');
                $payments[$payment->id]['vendor'] = $payment->reasons->lead()->vendor;
                $payments[$payment->id]['vendor_model'] = $payment->reasons->lead()->vendor_model;
                $payments[$payment->id]['vendor_year'] = $payment->reasons->lead()->vendor_year;
                $payments[$payment->id]['amount'] = $payment->amount;
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

       // dd($files);
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

        return view('home',[
            'name' => 'help',
            'data' => ''
        ]);
    }
    public function updates(){

        return view('home',[
            'name' => 'updates',
            'data' => ''
        ]);
    }
}
