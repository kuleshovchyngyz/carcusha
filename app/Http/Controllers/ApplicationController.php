<?php

namespace App\Http\Controllers;

use App\Models\Cars;
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
        return redirect()->route('home');
    }
    public function qrform(){

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

    function sendDataToBitrixGuzzle($method, $data) {
        // $webhook_url = "https://b24-4goccw.bitrix24.ru/rest/1/gfb5rzf8p5iwam80/";//test
        $webhook_url = "https://rosgroup.bitrix24.ru/rest/52/tvk30z03175k7x2p/";//real
        $res = Http::timeout(5)->post($webhook_url.$method ,$data);
        return json_decode($res->body(), 1);

    }

}
