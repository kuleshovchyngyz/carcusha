<?php

namespace App\Http\Controllers;

use App\Models\Cars;
use App\Models\Lead;
use App\Models\MessageNotification;
use App\Models\Paid;
use App\Models\Payment;
use App\Models\PaymentAmount;
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
use Illuminate\Support\Facades\Storage;
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
        dump($partners);
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
        $user_id = Auth::user()->id;
//        $pdf = new Fpdi();
//        $pdf->AddPage();
//
//        $filename = '/qrcodes/CARbusinessCard.pdf';
//        $pdf->setSourceFile(public_path($filename));
//
//        $template = $pdf->importPage(2);
//
//        $size = $pdf->getTemplateSize($template);
//
//        $pdf->useTemplate($template, 0, 0,$size['width'], $size['height'],true);
//        $pdf->Image( public_path('qrcodes/qrqrsmall_'.$user_id.'.png'),52.65, 15.44, 23.1, 23.1);
//        $pdf->Output();


//        require(base_path() . '/vendor/setasign/fpdf/makefont/makefont.php');
//
//        MakeFont(public_path('qrcodes/Calibri.ttf'),'cp1251');

        $QRPics = new QRCodeGenerator(route('car_application').'?id='.$user_id);
        if(!file_exists(public_path('/qrcodes/plakat'.Auth::user()->id.'.pdf'))){
            $QRPics->pdf_part_two();
        }
        if(!file_exists(public_path('/qrcodes/plakat'.Auth::user()->id.'.pdf'))){
            $QRPics->pdf_part_two();
        }

        //$pdf->Output();
        return view('home',[
            'name' => 'promo',
            'data' => ''
        ]);
    }
    public function downloadСard(){

        $file = public_path('/qrcodes/plakat'.Auth::user()->id.'.pdf');
        return Response::download($file);
    }
    public function downloadBusinessCard(){

        $file = public_path('/qrcodes/vizitka'.Auth::user()->id.'.pdf');
        return Response::download($file);
    }


    public function help(){

        return view('home',[
            'name' => 'help',
            'data' => ''
        ]);
    }
}
