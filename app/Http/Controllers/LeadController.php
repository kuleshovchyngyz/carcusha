<?php

namespace App\Http\Controllers;
use App\Clients\Bitrix;
use App\Models\balance;
use App\Models\Vendors;
use App\support\Bitrix\ApiConnect;
use App\support\Leads\DropDown;
use App\support\Leads\LeadBuilder;
use App\support\Leads\UpdatingLeadStatus;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Promise;
use App\Models\Lead;
use App\Models\Reason;
use App\Models\Payment;
use App\Models\MessageNotification;
use App\Models\Notification;
use App\Models\Status;
use App\Models\PaymentAmount;
use App\Models\User;
use Illuminate\Http\Request;
use Auth;
use DB;
use App\Models\Cars;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Http;
use App\Clients\SmsClient;
use App\Auth\Code;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
class LeadController extends Controller
{
    use HasRoles;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($origin=null)
    {

            $user_id = auth()->user()->id;

        if(!\File::exists('qrcodes/qrqr_'.$user_id.'.png')){
            $text = route('car_application').'?id='.$user_id;
            \QrCode::size(440)
                ->format('png')
                ->generate($text, public_path('qrcodes/qrqr_'.$user_id.'.png'));
        }
        $image_names = [];
        $leads = Lead::with('status','leadHistory')
            ->where('user_id',$user_id)
            ->orderBy('updated_at','DESC')
            ->get()
            ->each(function($item) use (&$image_names){
              if(\File::exists('uploads/'.$item->folder)) {
                  $filesInFolder = \File::files('uploads/'.$item->folder);
                  foreach($filesInFolder as $path) {
                      $file = pathinfo($path);
                      $image_names[$item->folder][] =  $file['basename'] ;
                  }
              }
            })
            ;
        $leadApi=[];
        if(Str::contains(Route::currentRouteName(), 'api')){
            foreach($leads as $key=>$lead){
                $leadApi[$key]['created_at']=$lead->created_at->format('d-m-Y');
                $leadApi[$key]['vendor']=$lead->vendor;
                $leadApi[$key]['vendor_model']=$lead->vendor_model;
                $leadApi[$key]['vendor_year']=$lead->vendor_year;
                $leadApi[$key]['lead_id']=$lead->id;
                if($lead->status->status_type!="finished"){
                    $leadApi[$key]['lead-folder']=$lead->folder;
                    $leadApi[$key]['lead-name']=$lead->vendor.' '.$lead->vendor_model.', '.$lead->vendor_year;
                    $leadApi[$key]['image-names']=implode('||',$images[$lead->folder] ?? []);
                    $leadApi[$key]['lead-id']=$lead->id;
                }
                if($lead->checked()){
                    $leadApi[$key]['danger']='Данный автомобиль обнаружен
                                                    на досках объявлений.';
                }
                $leadApi[$key]['phonenumber']=$lead->phonenumber;
                $leadApi[$key]['status_color']=$lead->color();
                $leadApi[$key]['status']=$lead->status->user_statuses->name;
                if($lead->status->user_statuses->comments!=''){
                    $leadApi[$key]['info-icon']= shortCodeParse($lead->status->user_statuses->comments);
                }
                $leadApi[$key]['total_money']=\ViewService::init($lead)->view('total_payments_by_lead');
                if(!$lead->is_on_pending() && $lead->all_amount()>0){
                    $leadApi[$key]['phonenumber']='Сумма заморожена, пока не
                                                       не завершаться переговоры';

                }
                foreach($lead->leadHistory as $key1=>$history){
                    $leadApi[$key]['history'][$key1]['created_at']=$history->created_at->format('d-m-Y H:i');
                    $leadApi[$key]['history'][$key1]['status_color']=color($history->event);
                    $leadApi[$key]['history'][$key1]['status']=$history->userStatus->name;
                    if($history->userStatus->comments!=''){
                        $leadApi[$key]['history'][$key1]['info-icon']=shortCodeParse($history->userStatus->comments);
                    }

                }
            }
//            return response()->json(['leads'=>$leads->toArray()],200);
            return response()->json(['leads'=>$leadApi],200);

        }

        return view('layouts.leads',[
            'images'=>$image_names,
            'leads' => $leads
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $buffer = new DropDown();
        return view('leads.create', [
            'buffer'=> $buffer->buffer(),
            'years'=>$buffer->get_car_years()
        ]);

    }

    public function update(Lead $lead)
    {
        $bitrix = new Bitrix();
        $bitrix->addDeal($lead->vendor,$lead->vendor_model,$lead->vendor_year,'',$lead->folder,$lead->status->index);
        $result = $bitrix->updateLead($lead->bitrix_lead_id);
        return $result;
    }
    public function photo(Request $request)
    {

        $file = file_get_contents( public_path('uploads').'/'.$request->folder.'/'.$request->file);

        //return 'uploads'.'/'.$request->folder.'/'.$request->file;
        return $file;

    }


    function sendDataToBitrix1($method, $data) {
        //$webhook_url = "https://b24-85lwia.bitrix24.ru/rest/1/s52ljoksktlyj1ed/";//test
//        $webhook_url = "https://carcusha.bitrix24.ru/rest/1/2dnfzer1t8dmyzt9/";//real
        $webhook_url = "https://rosgroup.bitrix24.ru/rest/52/abex7bx2vynnymu6/";//real
        $queryUrl = $webhook_url . $method ;
        $queryData = http_build_query($data);

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_POST => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_HTTPHEADER => array("Content-Type:multipart/form-data"),
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $queryUrl,
            CURLOPT_POSTFIELDS => $queryData,

        ));

        $result = curl_exec($curl);
        curl_close($curl);
        return json_decode($result, 1);
    }
    function sendDataToBitrixGuzzle($method, $data) {
        // $webhook_url = "https://b24-4goccw.bitrix24.ru/rest/1/gfb5rzf8p5iwam80/";//test

        $webhook_url = "https://rosgroup.bitrix24.ru/rest/52/dy6hojhc5p47lao6/";//real
        $res = Http::timeout(5)->post($webhook_url.$method ,$data);
        return json_decode($res->body(), 1);

    }


    public function get_status($id){
        $dealData = $this->sendDataToBitrixGuzzle('crm.lead.get', ['id' => $id] );
        $s = Status::where('index',$dealData['result']['STATUS_ID'])->first();
        return  $s->id;
    }
    public function send_to_tg_bot($message){

        $message1 = $message ;
        $data = ["companycode" => "coe5c447c2a168d", "data" => [["message" => $message1]]];
        $url = 'https://t.kuleshov.studio/api/getmessages';
        $data_string = json_encode($data);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        return true;
    }
    public function payments_to_reffered_user($user,$amount){
        if($user->user_who_referred()==false){

        }else{
            $r = Reason::where('reason','refer')->where('table_id',$user->id)->first();

            $percent = PaymentAmount::where('reason_of_payment','percentage')->first()->amount;
            Payment::create([
                'user_id' => $user->user_who_referred()->id,
                'reason'=>$r->id,
                'amount'=>$amount*$percent/100,
                'status'=>0,
                'status_group'=>'refer'
            ]);
            $balance = balance::where('user_id',$user->user_who_referred()->id)->first();
            $balance->balance = $balance->balance +  $amount*$percent/100;
        }
    }
    public function updateuserfields(Request $request){


    }
    public function getstatuses(Request $request,Bitrix $bitrix){
        if($request->all()['event'] == "ONCRMLEADUPDATE")
        {
            $l = Lead::where('bitrix_lead_id',$request->all()['data']['FIELDS']['ID'])->count();
            if($l>0){
                \Storage::disk('local')->append('example.txt', json_encode($request->all(),JSON_UNESCAPED_UNICODE));
                \Storage::disk('local')->append('sst.txt', $bitrix->getLeadStatus($request->all()['data']['FIELDS']['ID']));
                $lead_id =$request->all()['data']['FIELDS']['ID'];
                \Storage::disk('local')->append('ids.txt', $lead_id);
                new UpdatingLeadStatus($lead_id,$bitrix->getLeadStatus($lead_id));
                //App\support\Leads\UpdatingLeadStatus
            }
        }
    }
    public function cheching(){
        $this->reasons = Reason::where('reason_name','lead')->where('table_id', 121)->get();
        foreach ($this->reasons as $reason){
            $p = Payment::where('reason_id',$reason->id)->first();
            if($p){
                if($p->amount==2){
                    return true;
                }
            }
        }
        return false;
    }
    public function testing(){
        if(!$this->cheching()){
            echo 'inside';
        }else{
            echo 'outside';
        }
    }
    public function checkForInitial(){
        $status_id = 18;
        $lead_id = 121;
        $this->new_notification($lead_id,$status_id);
    }
    public function new_notification($id,$status){

    }


    public function send(){
        $sms = new SmsClient();
        $code = new Code();
        $sms->sendSms(+79111897638, "Ваш код: ".$code->generate(CODE::VERIFICATION));
        return $code->generate(CODE::VERIFICATION);
        //$this->send_to_tg_bot('maksat');
    }

    public function get_status_list1(){
        $dealData = $this->sendDataToBitrix1('crm.status.list',[
            'order'=> ["SORT"=> "ASC"],
            'filter'=> ["ENTITY_ID"=> "STATUS"]
        ]);
        //dump($dealData);
        //return  json_encode($dealData,JSON_UNESCAPED_UNICODE);
    }
    public function get_userfield_list1(){
        $dealData = $this->sendDataToBitrix1("crm.lead.fields",[

        ]);
        dump($dealData);
        //return  json_encode($dealData,JSON_UNESCAPED_UNICODE);
    }

    public function generateinvitation_code() {

        $users = User::count()==0 ? 1 : User::count();
        $number = mt_rand(1, 9999*($users%1000));
        $numlength = strlen(intval(strrev($number)));
        $number = intval((strrev($number))*(100000/(pow(10,$numlength))));
        $number = (strrev((string)$number));
        if ($this->invitation_codeExists($number)) {
            return $this->generateinvitation_code();
        }
        return $number;
    }
    public function invitation_codeExists($number) {
        return User::where('invitation_code',$number)->exists();
    }
    public function get_status_list(){
        //$dealData = $this->sendDataToBitrixGuzzle('crm.status.list', ['id' => $id] );
        //$dealData = $this->sendDataToBitrixGuzzle('crm.invoice.status.get', ['id' => 152 ] );
        $dealData = $this->sendDataToBitrix1('crm.status.list',[
            'order'=> ["SORT"=> "ASC"],
            'filter'=> ["ENTITY_ID"=> "STATUS"]
        ]);
        Status::truncate();
        foreach ($dealData['result'] as $status){
            Status::create([
                'index' =>$status['STATUS_ID'],
                'ID_on_bitrix' =>$status['ID'],
                'name' =>  $status['NAME'],
                'color' => $status['COLOR']
            ]);
            //dump($status);
            $this->sendDataToBitrixGuzzle('crm.status.add',[
                'fields' => $status
            ]);

        }
        //dump( json_encode($dealData['result'],JSON_UNESCAPED_UNICODE));
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //dd($request->all());
        $request->validate([
            'phone' => 'required',
        ]);

        $folder_name = $request->folder_id;
        $bitrix = new Bitrix();
        $bitrix->addDeal($request->car_vendor,$request->car_model,$request->car_year,$request->phone,$folder_name);
        $result = $bitrix->addLead();

        new LeadBuilder(
            isset($request->car_vendor) == true ? $request->car_vendor : '',
            isset($request->car_model) == true ? $request->car_model : '',
            isset($request->car_year) == true ? $request->car_year : '',
            isset($request->phone) == true ? $request->phone : '',
            isset($request->folder_id) == true ? $request->folder_id : '',
            Auth::user()->id,
            $result['result'],
            0
        );

        return redirect()->route('home');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Lead  $lead
     * @return \Illuminate\Http\Response
     */
    public function show(Lead $lead)
    {
        //
    }
    public function tester()
    {
//        $l = Lead::select('*')->first();
//        dd($l->statuse->leads);
        $s = Status::select('*')->get();

        foreach ($s as $si){
            //dump($si->toArray());ßß
//            DB::connection('mysql2')
//                ->table('statuses')
//                ->create([
//                   'name' => $si->name,
//                    'index' =>  $si->index,
//                    'color' =>  $si->color
//                ]);
        }
        $hooks = DB::connection('mysql2')
            ->table('checks')
            ->where('status',false)
            ->select('*')
            ->get();
        dd($hooks);

    }
    public function deleteimage(Request $request){
        \File::delete('uploads/'.$request->folder.'/'.$request->name);
        return  $request->folder;

    }
    public function testimage(Request $request){
//        return $request->all();
        $images = $request->file;

        $folder_name = $request->folder_id;

        if (!file_exists('uploads/'.$folder_name)) {
            \File::makeDirectory('uploads/'.$folder_name, $mode = 0777, true, true);
            // path does not exist
        }
        $files = \File::files('uploads/'.$folder_name);

        $image_names = array();
        if($images && count($files)<20){
            foreach ($images as  $key => $image){
                //return $image;
                if($key<20){
//                    $image->move(public_path('uploads/'.$folder_name),$image->getClientOriginalName());
                    file_put_contents('uploads/'.$folder_name.'/'.$request->number.'.txt', $image);
                    $image_names[]=$key;
                }
            }
        }
//        return $image_names;

        $path = public_path('uploads/'.$folder_name);
        $files = \File::files($path);
        return [$image_names,count($files) ];
        // return [$image_names,count($files)];
    }
    public function lead_status(Request $request)
    {
        if ($request->isJson()) {
            \Storage::disk('local')->put('example.txt', json_encode($request->all(),JSON_UNESCAPED_UNICODE));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Lead  $lead
     * @return \Illuminate\Http\Response
     */
    public function edit(Lead $lead)
    {
        //
    }
    public function telega(){
        $hooks = DB::connection('mysql2')
            ->table('checks')
            ->select('*')
            ->get();

        Notification::truncate();
        foreach ($hooks as $hook){
//            Lead::where('bitrix_lead_id', $hook->lead_id)
//                ->update([
//
//                ]);
            Notification::create([
                'lead_id' => $hook->lead_id,
                'status' => $hook->status,
                'event' => $hook->event,
                'application_token' => $hook->application_token,
            ]);
        }
        $leads = Lead::where('user_id',Auth::user()->id)->get();

        foreach ($leads as $lead){
            $n = Notification::where('lead_id',$lead->bitrix_lead_id)->get()->last();
            $st = Status::where('index',$n->status)->first();
            //dd($st->id);
            Lead::where('id',$lead->id)
                ->update([
                    'status_id' => $st->id
                ]);
        }
        return redirect()->back();

    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Lead  $lead
     * @return \Illuminate\Http\Response
     */
    public function destroy(Lead $lead)
    {
        //
    }
}
