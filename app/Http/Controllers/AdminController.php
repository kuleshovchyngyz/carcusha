<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use App\Models\Paid;
use App\Models\Payment;
use App\Models\PaymentAmount;
use App\Models\Question;
use App\Models\Status;
use App\Models\User;
use App\Models\UserStatuses;
use App\Models\Violation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
//        $queriesQuery = DB::table('users')
//            ->select(DB::raw("users.id as id , leads.checked as checked"))
//            ->rightJoin('users', 'users.id', '=', 'leads.user_id')->get();
//
//        $users = DB::table('users')
//            ->rightJoin('leads', 'users.id', '=', 'leads.user_id')
//            ->get();
//          dd($users);
        //$user =
        return view('admin.home',[
            'name' => 'users',
            'data' => User::all()
        ]);
    }
    public function user($id)
    {
        return view('admin.home',[
            'name' => 'user',
            'data' => User::find($id)
        ]);
    }
    public function SortByNumber($id,$type)
    {
        return view('admin.home',[
            'name' => 'user',
            'new' => $type,
            'data' => User::find($id)
        ]);
    }
    public function newleads(User $user)
    {
        return view('admin.home',[
            'name' => 'user',
            'new' => 1,
            'data' => $user
        ]);


    }
    public function verifyleads(User $user)
    {

        $user->leads_reviewed();
        return view('admin.home',[
            'name' => 'user',
            'data' => $user
        ]);


    }
    public function payments(){

        return view('admin.home',[
            'name' => 'payments',
            'data' => Paid::orderBy('status','DESC')->get()
        ]);
    }

    public function payments_settings(){

        $question = Question::all();
        $payment = PaymentAmount::all();
        return view('admin.home',[
            'name' => 'payment_settings',
            'data' => ['question'=>$question,'payment'=>$payment]
        ]);
    }
    public function settings()
    {
        $question = Question::all();
        $payment = PaymentAmount::all();
        return view('admin.home',[
            'name' => 'settings',
            'data' => ['question'=>$question,'payment'=>$payment]
        ]);
    }
    public function ads()
    {

        return view('admin.home',[
            'name' => 'ads',
            'data' => ''
        ]);
    }
    public function ban(User $user)
    {

        $user->active = !($user->active);
        $user->save();
        return redirect()->back();

    }

    public function report(User $user)
    {
       Violation::create(['user_id'=>$user->id]);
        return redirect()->back();
    }

    public function statuses(){
        $statuses = Status::all();
        return view('admin.home',[
            'name' => 'statuses',
            'data' => $statuses
        ]);
    }
     public function store_user_statuses(Request $request){
        $arr = [];
        $check = [];
        $request->request->remove('_token');
        foreach ($request->all() as $key=>$item){
            //if($item!=null){
                $value = explode('==',$key);
                if(!in_array($value[1],$check)){
                    $check[] = $value[1];
                    $arr[ $value[1] ] =  [ $value[0] => $item];
                }else{
                    $arr[ $value[1] ] = array_merge($arr[ $value[1] ],[  $value[0] => $item ]);
                }
            //}
        }
        //dd($arr[14]);
        PaymentAmount::where('reason_of_payment','initial')->update(['amount'=>$arr[1]['amount']]);
        PaymentAmount::where('reason_of_payment','success')->update(['amount'=>$arr[14]['amount']]);
        PaymentAmount::where('reason_of_payment','rejected')->update(['amount'=> -$arr[1]['amount']]);

        UserStatuses::truncate();

        foreach ($arr as $key => $item){
            $notify = 0;
            if(isset($item['notify'])){
                $notify = 1;
            }
            $s = UserStatuses::create([
                'status_id'=>$key,
                'name'=>$item['name'],
                'amount'=>$item['amount'],
                'notify'=> $notify
                ]);

        }
         return redirect()->back()->with('success_message', ['Сохранено']);
     }


    public function storeAds(Request $request){

        Ad::truncate();


        if(isset($request->ads)){
            Ad::create(['name'=>$request->ads]);
            $str = Ad::first()->name;
            $str = explode("\r\n", $str);
            return redirect()->back()->with('success_message', ['Сохранено']);
        }
    }
    public function store_payment_settings(Request $request)
    {
        PaymentAmount::where('reason_of_payment','initial')->update(['amount'=>$request->initial]);
        PaymentAmount::where('reason_of_payment','success')->update(['amount'=>$request->success]);
        PaymentAmount::where('reason_of_payment','refer')->update(['amount'=>$request->refer]);
        PaymentAmount::where('reason_of_payment','MinAmountOfPayment')->update(['amount'=>$request->MinAmountOfPayment]);
        $percent =$request->percentage;
        $percent = str_replace("%", "", $percent);
        PaymentAmount::where('reason_of_payment','percentage')->update(['amount'=>$percent]);
        return redirect()->back()->with('success_message', ['Сохранено']);
    }
    public function pay_to_partner(Paid $paid)
    {
        $paid->status = 'complete';
        $paid->save();
        $user = User::find($paid->user_id);
        $balance = $user->balance;
        $balance->balance = $balance->balance -  $paid->amount;
        $balance->save();
        return true;

    }
    public function calculate_available_amount(){
        $user_id = 57;
        $payments = Payment::where('user_id',$user_id)->get();
        $arr = [];
        foreach ($payments as $payment){
            if(!$payment->pending_amount->status){
                $arr[] = $payment->pending_amount->payment_id;
            }
        }
        $payments = Payment::whereIn('id',$arr)->get();
        $sum = 0;
        foreach ($payments as $payment){
            $sum += $payment->payment_amount->amount;
        }

    }

    public function store_settings(Request $request)
    {

        $request->validate([

            'questions.*'=>'required',
            'answers.*'=>'required'
        ]);

            if(isset($request->question_id)){

                foreach ($request->question_id as $key => $question_id){
                    Question::find($question_id)->update([
                        'question'=> $request->questions[$key],
                        'answer'=>$request->answers[$key]
                    ]);
                }
                for ($i = count($request->question_id); $i < count($request->questions); $i++){
                    Question::create([
                        'question'=> $request->questions[$i],
                        'answer'=>$request->answers[$i]
                    ]);
                }
            }else{
                foreach ($request->questions as $key => $question){
                    Question::create([
                        'question'=> $request->questions[$key],
                        'answer'=>$request->answers[$key]
                    ]);
                }
            }

        return redirect()->back()->with('success_message', ['Сохранено']);






    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
