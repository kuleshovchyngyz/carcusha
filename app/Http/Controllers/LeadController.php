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
class LeadController extends Controller
{
    use HasRoles;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $leads = Lead::with('status','leadHistory')
            ->where('user_id',Auth::user()->id)
            ->orderBy('updated_at','DESC')
            ->get();
        //dd($leads);

        return view('home',[
            'name' => 'leads',
            'data' => $leads
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


    function sendDataToBitrix1($method, $data) {
        //$webhook_url = "https://b24-85lwia.bitrix24.ru/rest/1/s52ljoksktlyj1ed/";//test
        $webhook_url = "https://carcusha.bitrix24.ru/rest/1/rrr2v2vfxxbw2jeo/";//real
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
    public function addDeal($vendor, $model,$year,$img,$phone,$folder_name) {
        $years = [
            "2008" => "295",
            "2009" => "293",
            "2010" => "67",
            "2011" => "65",
            "2012" => "63",
            "2013" => "61",
            "2014" => "59",
            "2015" => "57",
            "2016" => "55",
            "2017" => "53",
            "2018" => "51",
            "2019" => "49",
            "2020" => "47",
            "2021" => "45",
            "2022" => "291"
        ];

        $data_img=[];
        $crm_pics_field_name = ['UF_CRM_1633362445295','UF_CRM_1633362456303','UF_CRM_1633362468187','UF_CRM_1633362478787','UF_CRM_1633362488670','UF_CRM_1637847699599','UF_CRM_1637847730399','UF_CRM_1637847742893','UF_CRM_1637847753241','UF_CRM_1637847764708',
            'UF_CRM_1642672762','UF_CRM_1642673006','UF_CRM_1642673046','UF_CRM_1642673077','UF_CRM_1642673107','UF_CRM_1642673125','UF_CRM_1642673174','UF_CRM_1642673190','UF_CRM_1642673211','UF_CRM_1642673231'];//real
        foreach ($img as $key => $i){
            $data_img[] =
                curl_file_create(public_path('uploads').'/'.$folder_name.'/'.$img[$key],'image/*',$img[$key]);
        }
//
        //
        $array = [];
        foreach ($data_img as $key=>$ready){
            $file_type = $this->getB64Type(file_get_contents($data_img[$key]->name));

            switch($file_type) {
                case 'image/gif':
                    $file_ext = 'gif';
                    break;
                case 'image/png':
                    $file_ext = 'jpg';
                    break;
                case 'image/jpeg':
                case 'image/jpg':
                default:
                    $file_ext = 'jpg';
                    break;
            }
            $data = explode( ',', file_get_contents($data_img[$key]->name) );

            $array[$crm_pics_field_name[$key]] = ['fileData'=>[
                $key.'.'.$file_ext,$data[ 1 ]
            ]];
        }

        $v = isset($vendor)==true ? $vendor :"";
        $car = isset($model)==true ? $model :"";

        if(env('BITRIX_HEADER')=='test'){
            $array['TITLE'] = "Тестовое";
        }
        if(env('BITRIX_HEADER')=='real'){
            $array['TITLE'] =  $v.' '.$car;
        }

        $array['UF_CRM_1633361973449'] = $v.' '.$car; //real   "ID" => "312"

        $array['UF_CRM_1633362091686'] =  isset($year)==true ? [$years[$year]] :""; //real

        $array['SOURCE_ID'] = "1";
        $array['PHONE'] =  [['VALUE' => $phone, 'VALUE_TYPE' => 'WORK']];
        $bitrix = new Bitrix();
        $dealData = $bitrix->addLead($array);
        return $dealData;
    }
    public         function getB64Type($str) {
    // $str should start with 'data:' (= 5 characters long!)
    return substr($str, 5, strpos($str, ';')-5);
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
        $sms->sendSms(+996708277186, "Ваш код: ".$code->generate(CODE::VERIFICATION));
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
        $image_names = array();
        $folder_name = $request->folder_id;

        if(\File::exists('uploads/'.$folder_name)) {
            $filesInFolder = \File::files('uploads/'.$folder_name);
            foreach($filesInFolder as $path) {
                $file = pathinfo($path);
                $image_names[] =  $file['basename'] ;
            }

            $result = $this->addDeal($request->car_vendor,$request->car_model,$request->car_year,$image_names,$request->phone,$folder_name);
        }else{
            $result = $this->addDeal($request->car_vendor,$request->car_model,$request->car_year,$image_names,$request->phone,$folder_name);
        }


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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Lead  $lead
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Lead $lead)
    {
        //
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
