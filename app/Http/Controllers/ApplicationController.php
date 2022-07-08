<?php

namespace App\Http\Controllers;

use App\Clients\Bitrix;
use App\Models\Cars;
use App\Models\PublicOffer;
use App\Models\User;
use App\Models\Vendors;
use App\support\Leads\DropDown;
use App\support\Leads\LeadBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class ApplicationController extends Controller
{
    public function store(Request $request){

        $request->validate([
            'phone' => 'required',
        ]);
        $folder_name = $request->folder_id;
        $bitrix = new Bitrix();
        $bitrix->addDeal($request->car_vendor,$request->car_model,$request->car_year,$request->phone,$folder_name);
        $result = $bitrix->addLead();


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

        return view('leads.thanks');
    }
    public function qrform(){
        $buffer = new DropDown();
        return view('leads.qrcreate', [
            'buffer'=> $buffer->buffer(),
            'name' => 'qrform',
            'years'=>$buffer->get_car_years()
        ]);

    }
    public function thanks(){

        return view('leads.thanks');

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
            "other" => "309",
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
        $crm_pics_field_name = ['UF_CRM_1633362445295','UF_CRM_1633362456303','UF_CRM_1633362468187','UF_CRM_1633362478787','UF_CRM_1633362488670','UF_CRM_1637847699599','UF_CRM_1637847730399','UF_CRM_1637847742893','UF_CRM_1637847753241','UF_CRM_1637847764708'];//real
        foreach ($img as $key => $i){
            $data_img[] =
                curl_file_create(public_path('uploads').'/'.$folder_name.'/'.$img[$key],'image/*',$img[$key]);
        }

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
    public function checkPromo(Request $request){
//        return $request->header('Accept');
//        return response()->json(['mk'=>Route::currentRouteName()],200);
        if (User::where('invitation_code', '=', $request->promo)->count() > 0 ) {
            if(Str::contains(Route::currentRouteName(), 'api')){
                return response()->json(true,200);
            }
            return 'yes';
        }
        if(Str::contains(Route::currentRouteName(), 'api')){
            return response()->json(false,200);
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
