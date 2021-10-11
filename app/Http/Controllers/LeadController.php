<?php

namespace App\Http\Controllers;
use App\Models\balance;
use App\support\Bitrix\ApiConnect;
use App\support\Leads\LeadBuilder;
use App\support\Leads\UpdatingLeadStatus;
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


        //auth()->user()->assignRole('admin');
        $leads = Lead::select('*')
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
        $userId = Auth::id();
        $result = Cars::select('*')
            ->where('vendor','!=','')
            ->orderBy('vendor','ASC')
            ->get()
            ->groupBy('vendor');

        $buffer = '<option value="">Марка авто</option>';
        foreach($result as $car_vendor => $car_vendor_data) {
            $car_vendor = stripslashes($car_vendor);
            $buffer .= '<option value="'.$car_vendor.'">'.$car_vendor.'</option>';
        }
        //dd($buffer);
        return view('leads.create', [
            'buffer'=> $buffer,
            'years'=>$this->get_car_years()
        ]);



    }
    public function get_car_years() {
        $buffer = '<option value=2005>'.'2005'.' и старше</option>';
        for($i = date("Y")-15; $i < date("Y"); $i++){
            $buffer .= '<option value="'.$i.'">'.$i.'</option>';
        }
        return $buffer;
    }
    public function get_car_models()
    {
        $car_vendor = trim($_REQUEST['car_vendor']);
        $result = Cars::select('*')
            ->where('vendor',$car_vendor)
            ->where('model','!=','')
            ->orderBy('model','ASC')
            ->get()
            ->groupBy('model');
        //dd($result);
        $buffer = '<option value="">Модель авто</option>';

        foreach($result as $car_model => $car_model_data) {

            $car_model = stripslashes($car_model);
            $buffer .= '<option value="'.$car_model.'">'.$car_model.'</option>';
        }
        $buffer .= '<option value="Другая модель">Другая модель</option>';
        return $buffer;
    }
    function sendDataToBitrix1($method, $data) {
        //$webhook_url = "https://b24-85lwia.bitrix24.ru/rest/1/s52ljoksktlyj1ed/";//test
        $webhook_url = "https://rosgroup.bitrix24.ru/rest/52/tvk30z03175k7x2p/";//real
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

        $webhook_url = "https://rosgroup.bitrix24.ru/rest/52/tvk30z03175k7x2p/";//real
        $res = Http::timeout(5)->post($webhook_url.$method ,$data);
        return json_decode($res->body(), 1);

    }


    function sendDataToBitrix($method, $data) {
        //$webhook_url = "https://b24-4goccw.bitrix24.ru/rest/1/gfb5rzf8p5iwam80/";//test
        $webhook_url = "https://rosgroup.bitrix24.ru/rest/52/tvk30z03175k7x2p/";//real
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
    public function addDeal($vendor, $model,$year,$img,$phone,$folder_name) {
        $years = [
            "2005" => "987",
            "2006" => "763",
            "2007" => "759",
            "2008" => "330",
            "2009" => "328",
            "2010" => "326",
            "2011" => "324",
            "2012" => "322",
            "2013" => "320",
            "2014" => "318",
            "2015" => "304",
            "2016" => "316",
            "2017" => "302",
            "2018" => "314",
            "2019" => "300",
            "2020" => "312"
        ];
        $data_img=[];
        //$crm_pics_field_name = ['UF_CRM_1627988390','UF_CRM_1625740903','UF_CRM_1627988418','UF_CRM_1627988441'];//test
        $crm_pics_field_name = ['UF_CRM_1526660196','UF_CRM_1541502441','UF_CRM_1541502472','UF_CRM_1541502488'];//real
        foreach ($img as $key => $i){
            $data_img[] =
                curl_file_create(public_path('uploads').'/'.$folder_name.'/'.$img[$key],'image/*',$img[$key]);
        }
        $array = [];
        foreach ($data_img as $key=>$ready){
            $array[$crm_pics_field_name[$key]] = ['fileData'=>[
                $data_img[$key]->postname,base64_encode(file_get_contents($data_img[$key]->name))
            ]];
        }
        $array['TITLE'] = "Тестовое";
		$v = isset($vendor)==true ? $vendor :"";
		$car = isset($model)==true ? $model :"";

        $array['UF_CRM_1526660063'] = $v.' '.$car; //real   "ID" => "312"

        $array['UF_CRM_1526659328'] =  isset($year)==true ? [$years[$year]] :""; //real
        // $array['UF_CRM_1625740869'] = $v.' '.$car;

        // $array['UF_CRM_1625740924'] = isset($year)==true ? $year :"";
        $array['SOURCE_ID'] = "5";
        $array['PHONE'] =  [['VALUE' => $phone, 'VALUE_TYPE' => 'WORK']];
        $dealData = $this->sendDataToBitrixGuzzle('crm.lead.add', [
            'fields' => $array,
            'params' => [
                'REGISTER_SONET_EVENT' => 'Y'
            ],
        ]);
        return $dealData;
    }
    public function fields(){
        $u = User::find(2)->notifier;
        dump($u);
        //$dealData = $this->sendDataToBitrixGuzzle('crm.status.fields',array());
//        $dealData = $this->sendDataToBitrixGuzzle('crm.lead.fields',array());
//        "UF_CRM_1526732995" => "yesdo"
//    "UF_CRM_1526733011" => "nono"
      //  $dealData = new ApiConnect('crm.lead.get', ['id' => '771965']);
        //dump($dealData->getResponse());
//        dump($dealData->getFieldName('isInAuto.ru'));
//        $dealData = $this->sendDataToBitrixGuzzle('crm.lead.get', ['id' => '77965'] );
//        dump($dealData);
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
    public function getstatuses(Request $request){
        if($request->all()['event'] == "ONCRMLEADUPDATE")
        {
			$l = Lead::where('bitrix_user_id',$request->all()['data']['FIELDS']['ID'])->count();
			if($l>0){
			    \Storage::disk('local')->append('example.txt', json_encode($request->all(),JSON_UNESCAPED_UNICODE));
                \Storage::disk('local')->append('sst.txt', $this->get_status($request->all()['data']['FIELDS']['ID']));
                $lead_id = Lead::where('bitrix_user_id',$request->all()['data']['FIELDS']['ID'])->first()->bitrix_user_id;
                new UpdatingLeadStatus($lead_id,$this->get_status($request->all()['data']['FIELDS']['ID']));
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

    public function ready_to_sell(){

    }
    public function notifications($id,$status)
    {
        $l = Lead::where('bitrix_user_id',$id)->first();
        $l->status_id = $status;
        $l->save();

        $user = User::find($l->user_id);
        $l_ids = Lead::where('user_id', $l->user_id)->pluck('bitrix_user_id');
        $statuses = Status::pluck('name','index');
        $hooks = Notification::whereIn('lead_id',$l_ids)
            ->orderby('updated_at','DESC')
            ->get();
        $notes = array();
        $fiststatuses = ['В работе','Ожидаем фото','На оценку','На повторную оценку','Оценка без фото','Сообщить цену','Клиент сомневается','Ожидаем решения клиента','Конкуренты предлагают больше','Другое','Судебное'];
        foreach ($l_ids as $l_id){
            $seps = $hooks->where('lead_id',$l_id);
            //dd();
            if($l->bitrix_user_id==$l_id){
                if(count($seps)>1){
                    $str = "";
                    $changed = "";
                    $key = 0;
					$changed_id = '20';
                    foreach ($seps as  $sep){
                        if ($key==0){
                            $date = date('d.m.Y', strtotime($sep->updated_at));
                            $time = date('H:i:s', strtotime($sep->updated_at));
                            $str = $date.' в '.$time.' у лида #'.$sep->lead_id.' изменился статус с ';
                            $changed = $statuses[$sep->status];
							 $changed_id = $sep->status;
                        }else if($key==1){
							if($statuses[$sep->status]!=$changed){
								$str = $str.'"'.$statuses[$sep->status].'"'.' на "'.$changed.'"' ;
							}else{
								$str = 'false';
							}


                            if ($changed == "Согласен продать"){
                                $amount = PaymentAmount::where('reason_of_payment','success')->first()->amount;
                                $this->payments_to_reffered_user($user,$amount);
                                $reason = Reason::create([
                                    'table_id'=>$l_id,
                                    'reason'=> 'lead'
                                ]);
                                Payment::create([
                                    'user_id' => $l->user_id,
                                    'reason'=>$reason->id,
                                    'amount'=>$amount,
                                    'status'=>0,
                                    'status_group'=>'success'
                                ]);
                                $balance = balance::where('user_id',$l->user_id)->first();
                                $balance->balance = $balance->balance +  $amount;
                                $str = $str.'. <sapn class="text-danger">'.$amount.' ₽</sapn>';
                            }
                            if (in_array($changed,$fiststatuses)){
                                $reason= Reason::where('table_id',$l_id)->where('reason','lead');

                                if($reason->count()==0){
                                    $amount = PaymentAmount::where('reason_of_payment','initial')->first()->amount;
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
                                    $reason = Reason::create([
                                        'table_id'=>$l_id,
                                        'reason'=> 'lead'
                                    ]);
                                    Payment::create([
                                        'user_id' => $l->user_id,
                                        'reason'=>$reason->id,
                                        'amount'=>$amount,
                                        'status'=>0,
                                        'status_group'=>'initial'.$changed_id
                                    ]);
                                    $balance = balance::where('user_id',$l->user_id)->first();
                                    $balance->balance = $balance->balance +  $amount;
                                    $str = $str.'. <sapn class="text-danger">'.$amount.' ₽</sapn>';
                                }else{
                                    $reason = $reason->first();
                                    $is_registered_payment = Payment::where('user_id',$l->user_id)->where('reason',$reason->id)->where('status_group','like','%initial%')->count();
                                    if($is_registered_payment==0){
                                        $amount = PaymentAmount::where('reason_of_payment','initial')->first()->amount;
                                        if($user->user_who_referred()==false){

                                        }else{
                                            $r = Reason::where('reason','refer')->where('table_id',$user->id)->first();
                                            $percent = PaymentAmount::where('reason_of_payment','percentage')->first()->amount;

                                            Payment::create([
                                                'user_id' => $user->user_who_referred()->id,
                                                'reason'=>$r->id,
                                                'amount'=>floor($amount*$percent/100),
                                                'status'=>0,
                                                'status_group'=>'refer'
                                            ]);
                                            $balance = balance::where('user_id',$user->user_who_referred()->id)->first();
                                            $balance->balance = $balance->balance +  floor($amount*$percent/100);
                                        }
                                        Payment::create([
                                            'user_id' => $l->user_id,
                                            'reason'=>$reason->id,
                                            'amount'=>$amount,
                                            'status'=>0,
                                            'status_group'=>'initial'.$changed_id

                                        ]);
                                        $balance = balance::where('user_id',$l->user_id)->first();
                                        $balance->balance = $balance->balance +  $amount;
                                        $str = $str.'. <sapn class="text-danger">'.$amount.' ₽</sapn>';
                                    }
                                }


                            }
                            if ($changed =="Жесткий негатив"){
                                $str = $str.'.  Лид вышел из работы.';
                            }

                            $notes[] = $str;
							if($str!='false'){
							MessageNotification::create([
                                'user_id'=>$l->user_id,
                                'seen'=>false,
                                'message'=>$str,
                                'lead_id'=>$l_id
                            ]);
							$this->send_to_tg_bot($str);

							}

                            //dump($str);
                        }
                        $key++;

                        // dump(Carbon::createFromFormat('Y-m-d H:i:s', $sep->updated_at)->format('d-m-Y'));
                    }
                }
            }
        }
    }
    public function send(){
        $this->send_to_tg_bot('maksat');
    }

    public function get_status_list1(){
        $dealData = $this->sendDataToBitrix1('crm.status.list',[
            'order'=> ["SORT"=> "ASC"],
            'filter'=> ["ENTITY_ID"=> "STATUS"]
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
            dump($status);
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
            dump($si->toArray());
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
        $images = $request->file;
        $folder_name = $request->folder_id;
        $image_names = array();
        if($images){
            foreach ($images as  $key => $image){
                if($key<4){
                    $image->move(public_path('uploads/'.$folder_name),$image->getClientOriginalName());
                    $image_names[]=$image->getClientOriginalName();
                }
            }
        }
        return $image_names;
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
//            Lead::where('bitrix_user_id', $hook->lead_id)
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
            $n = Notification::where('lead_id',$lead->bitrix_user_id)->get()->last();
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
