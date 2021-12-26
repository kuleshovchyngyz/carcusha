<?php

namespace App\Http\Controllers;

use App\Clients\Bitrix;
use App\Models\Cars;
use App\Models\PublicOffer;
use App\Models\User;
use App\Models\Vendors;
use App\support\Leads\LeadBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ApplicationController extends Controller
{
    public function store(Request $request){

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
//        dd($result);
        if(isset($request->user_id)){
            new LeadBuilder(
                isset($request->car_vendor) == true ? $request->car_vendor : '',
                isset($request->car_model) == true ? $request->car_model : '',
                isset($request->car_year) == true ? $request->car_year : '',
                isset($request->phone) == true ? $request->phone : '',
                isset($request->folder_id) == true ? $request->folder_id : '',
                $request->user_id,
                $result['result'],
                1
            );
        }
        $path = public_path('uploads/'.$folder_name);
        \File::deleteDirectory($path);
        return redirect()->route('home');
    }
    public function qrform(){
        $results = Vendors::orderBy('name','ASC')->get();
        $buffer = '<option value="">Марка авто</option>';
        foreach($results as $result) {
            $car_vendor = stripslashes($result->name);
            $buffer .= '<option value="'.$car_vendor.'">'.$car_vendor.'</option>';
        }
        //dd($buffer);
        return view('leads.qrcreate', [
            'buffer'=> $buffer,
            'name' => 'qrform',
            'years'=>$this->get_car_years()
        ]);

    }
    public function get_car_years(): string
    {
        $buffer = '<option value=2005>'.'2005'.' и старше</option>';
        for($i = date("Y")-15; $i < date("Y"); $i++){
            $buffer .= '<option value="'.$i.'">'.$i.'</option>';
        }
        return $buffer;
    }
    public function addDeal($vendor, $model,$year,$img,$phone,$folder_name) {
        $years = [
            "2005" => "987",
            "2006" => "763",
            "2007" => "759",
            "2008" => "330",
            "2009" => "328",
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
            "2021" => "45"
        ];
        $data_img=[];
        //$crm_pics_field_name = ['UF_CRM_1627988390','UF_CRM_1625740903','UF_CRM_1627988418','UF_CRM_1627988441'];//test
        $crm_pics_field_name = ['UF_CRM_1633362445295','UF_CRM_1633362456303','UF_CRM_1633362468187','UF_CRM_1633362478787','UF_CRM_1633362488670','UF_CRM_1637847699599','UF_CRM_1637847730399','UF_CRM_1637847742893','UF_CRM_1637847753241','UF_CRM_1637847764708'];//real
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
        // $array['UF_CRM_1625740869'] = $v.' '.$car;

        // $array['UF_CRM_1625740924'] = isset($year)==true ? $year :"";
        $array['SOURCE_ID'] = "1";
        $array['PHONE'] =  [['VALUE' => $phone, 'VALUE_TYPE' => 'WORK']];

        $bitrix = new Bitrix();
        $dealData = $bitrix->addLead($array);
//        $dealData = $this->sendDataToBitrixGuzzle('crm.lead.add', [
//            'fields' => $array,
//            'params' => [
//                'REGISTER_SONET_EVENT' => 'Y'
//            ],
//        ]);
        // dd($dealData);
        return $dealData;
    }
    public function checkPromo(Request $request){
        if (User::where('invitation_code', '=', $request->promo)->count() > 0 ) {
            return 'yes';
        }
        return 'no';
    }

    public function publicOffer(){
        return view('layouts.publicOffer');
    }

    function sendDataToBitrixGuzzle($method, $data) {
        // $webhook_url = "https://b24-4goccw.bitrix24.ru/rest/1/gfb5rzf8p5iwam80/";//test
        $webhook_url = "https://rosgroup.bitrix24.ru/rest/52/tvk30z03175k7x2p/";//real
        $res = Http::timeout(5)->post($webhook_url.$method ,$data);
        return json_decode($res->body(), 1);

    }

}
