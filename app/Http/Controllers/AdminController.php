<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use App\Models\MessageNotification;
use App\Models\Paid;
use App\Models\Payment;
use App\Models\PaymentAmount;
use App\Models\PublicOffer;
use App\Models\Question;
use App\Models\Refer;
use App\Models\SiteSetting;
use App\Models\Status;
use App\Models\Updates;
use App\Models\User;
use App\Models\UserPaymentAmount;
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
    public function index(Request $request)
    {


        $sort = $request->get('sort', 'desc');
        $users = User::with(['setting','leads','balance','violations','paids','roles','refers'])
            ->whereHas(
                'roles', function($q){
                $q->where('name', 'user');
            })
            ->withSum(['paids as paid'=>function($query){
                $query->where('status','complete');
            }],'amount')
            ->withCount(['leads as pending'=> function($query){
                $userStatuses = UserStatuses::where('amount','nothing')->orwhere('amount','initial')->pluck('id');
                $query->whereIn('status_id',$userStatuses);
            }])
            ->withCount(['leads as rejected'=> function($query){
                $userStatuses = UserStatuses::where('amount','rejected')->pluck('id');
                $query->whereIn('status_id',$userStatuses);
            }])
            ->withCount(['leads as successful'=> function($query){
                $userStatuses = UserStatuses::where('amount','success')->pluck('id');
                $query->whereIn('status_id',$userStatuses);
            }])
            ->withCount(['leads as numberOfNewLeads' => function ($query) {
            $query->where('checked', 0);
        }])
            ->get();
      if($sort == 'asc'){
            $users = $users->sortBy(function ($users) {
                return $users->leads->where('checked',0)->count();
            });
        }else if($sort == 'desc'){
          $users = $users->sortByDesc(function ($users) {
              return $users->leads->where('checked',0)->count();
          });
        }
        $sort = ($sort == 'asc') ? 'desc' : 'asc';
        return view('admin.home',[
            'name' => 'users',
            'data' =>[
                'users'=>$users,
                'sort' => $sort
            ]
        ]);
    }
    public function user($id)
    {
        return view('admin.home',[
            'name' => 'user',
            'data' => User::find($id)
        ]);
    }
    public function changePaymentType(Request $request)
    {


        $data = array();

        $user = User::find($request['user']);

        $user->unique_payment =  $request['switch'] == 'true';
        $user->save();

        if($user->UserPaymentAmounts->count()==0){
            $arr = PaymentAmount::pluck('amount','reason_of_payment');
            foreach ($arr as $key =>$item){
                $data[] =  ['user_id'=>$request['user'], 'reason_of_payment'=> $key, 'amount'=>str_replace('%','',$item)];
            }
            UserPaymentAmount::insert($data);
        }
        return ($data);
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
    public function payments(Request $request){

        $sort = $request->get('sort', 'asc');
       // dd($request->all());
        $paids = Paid::orderBy('status', $sort)->get();
        $sort = ($sort == 'asc') ? 'desc' : 'asc';
        //$url = GenerateRouteOrder:url($sort) // http://partnersonserver/admin?sort
        return view('admin.home',[
            'name' => 'payments',
            'data' => [
                'sort'=>$sort,
                'paids' =>$paids
            ]
        ]);
    }

    public function addBot(Request $request){
       SiteSetting::truncate();
        SiteSetting::create(['value'=>$request->token,'name'=>'telegramBotToken']);
        return redirect()->back()->with('success_message', [__('Сохранено')]);
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
    public function updates()
    {
        $updates = Updates::all();
        return view('admin.home',[
            'name' => 'updates',
            'data' => ['updates'=>$updates]
        ]);
    }
    public function publicOffers()
    {
       $all = PublicOffer::all();
       $text = '';
       $title = '';
       if($all->count()==2){
            $text = $all[1]->text;
            $title = $all[0]->text;
       }
        return view('admin.home',[
            'name' => 'publicOffer',
            'data' => [$title,$text]
        ]);
    }
    public function storePublicOffers(Request $request)
    {
        PublicOffer::truncate();
        PublicOffer::create(['text'=>$request->title]);
        PublicOffer::create(['text'=>$request->text]);
        return redirect()->back()->with('success_message', ['Сохранено']);
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

    public function report(Request $request,User $user)
    {
        Violation::create(['user_id'=>$user->id,'reason'=>$request->reason]);
        MessageNotification::create([
            'user_id'=>$user->id,
            'seen'=>false,
            'message'=>$request->reason,
            'lead_id'=>-3,
        ]);
        return redirect()->back()->with('success_message', ['Сохранено']);
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
//dd($arr);

        UserStatuses::truncate();

        foreach ($arr as $key => $item){

            $status = Status::find($key);
            $status->color = $item['color'];
            $status->save();
            $notify = 0;
            if(isset($item['notify'])){
                $notify = 1;
            }

            $s = UserStatuses::create([
                'status_id'=>$key,
                'name'=>$item['name'],
                'amount'=>$item['amount'],
                'comments'=>$item['comments'],
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

    public function user_payment_settings($id){
        $payment = PaymentAmount::all();
        return view('admin.home',[
            'name' => 'user',
            'data' => User::find($id),
            'file'=> [
                'user'=>User::find($id),
                'include'=>'admin.user_payment_settings'
            ]
//            'name' => 'user_payment_settings',
//            'data' => $payment
        ]);

    }

    public function store_payment_settings(Request $request)
    {
        PaymentAmount::where('reason_of_payment','initial')->update(['amount'=>$request->initial]);
        PaymentAmount::where('reason_of_payment','success')->update(['amount'=>$request->success]);
        PaymentAmount::where('reason_of_payment','nothing')->update(['amount'=>$request->nothing]);
        PaymentAmount::where('reason_of_payment','rejected')->update(['amount'=>$request->rejected]);
        PaymentAmount::where('reason_of_payment','refer')->update(['amount'=>$request->refer]);
        PaymentAmount::where('reason_of_payment','MinAmountOfPayment')->update(['amount'=>$request->MinAmountOfPayment]);
        PaymentAmount::where('reason_of_payment','firstPayment')->update(['amount'=>$request->firstPayment]);
        $percent =$request->percentage;
        $percent = str_replace("%", "", $percent);
        PaymentAmount::where('reason_of_payment','percentage')->update(['amount'=>$percent]);
        return redirect()->back()->with('success_message', ['Сохранено']);
    }
    public function store_user_payment_settings(Request $request)
    {
        $request->request->remove('_token');

        $data = array();
        $user_id = $request['user_id'];

        $request->request->remove('user_id');

        foreach ($request->all() as $key =>$item){
            $data[] =  ['user_id'=>$user_id, 'reason_of_payment'=> $key, 'amount'=>$item];
            UserPaymentAmount::where('user_id',$user_id)->where('reason_of_payment',$key)->update(['amount'=>str_replace('%','',$item)]);
        }




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
            $sum += $payment->payment_amount()->amount;
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

    public function store_updates(Request $request)
    {

        $request->validate([
            'versions.*'=>'required',
            'changes.*'=>'required'
        ]);

            if(isset($request->version_id)){

                foreach ($request->version_id as $key => $version_id){
                    Updates::find($version_id)->update([
                        'version'=> $request->versions[$key],
                        'changes'=>$request->changes[$key]
                    ]);
                }
                for ($i = count($request->version_id); $i < count($request->versions); $i++){
                    Updates::create([
                        'version'=> $request->versions[$i],
                        'changes'=>$request->changes[$i]
                    ]);
                }
            }else{
                foreach ($request->versions as $key => $version){
                    Updates::create([
                        'version'=> $request->versions[$key],
                        'changes'=>$request->changes[$key]
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
