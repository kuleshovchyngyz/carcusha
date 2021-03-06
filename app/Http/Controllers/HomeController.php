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
        $this->middleware('auth');
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
        $messages = MessageNotification::where('user_id',Auth::user()->id)->get();
        if(Str::contains(Route::currentRouteName(), 'api')){
            dd(111);
            return  $messages;
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
        //dd($leads);
        return view('home',[
            'name' => 'payments',
            'data' => $leads
        ]);
    }
    public function paymentqueries(){
        $paid = Paid::where('user_id',\auth()->user()->id)->get();
        //dd($leads);
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
    public function download??ard(){

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
