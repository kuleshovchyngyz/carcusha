<?php

namespace App\Clients;
use App\Models\Status;
use Illuminate\Support\Facades\Http;

class Bitrix
{
    const years = ["2008" => "295", "2009" => "293","2010" => "67","2011" => "65","2012" => "63","2013" => "61","2014" => "59","2015" => "57","2016" => "55","2017" => "53","2018" => "51","2019" => "49","2020" => "47","2021" => "45","2022" => "291"];
    const  crm_pics_field_name = ['UF_CRM_1633362445295','UF_CRM_1633362456303','UF_CRM_1633362468187','UF_CRM_1633362478787','UF_CRM_1633362488670','UF_CRM_1637847699599','UF_CRM_1637847730399','UF_CRM_1637847742893','UF_CRM_1637847753241','UF_CRM_1637847764708',
            'UF_CRM_1642672762','UF_CRM_1642673006','UF_CRM_1642673046','UF_CRM_1642673077','UF_CRM_1642673107','UF_CRM_1642673125','UF_CRM_1642673174','UF_CRM_1642673190','UF_CRM_1642673211','UF_CRM_1642673231'];//real
    protected $http;
    protected $debug;
    protected $error = true;
    protected $result;
    protected $bitrix_data = [];
    public function __construct()
    {
        $this->http = env('BITRIX_CARCUSHA_WEBHOOK_URL');
        $this->result = array();
    }

    public function connect($method, $data) {
//        $this->http =  "https://carcusha.bitrix24.ru/rest/1/faad1wbn834vv030/";
        $res = Http::timeout(10)->post($this->http.$method ,$data);
        $this->result =  json_decode($res->body(), 1);
        return $this->result;
    }
    public function getResponse(){
        if(!array_key_exists('error_description',$this->result)){
            return $this->result;
        }
        return false;
    }

    public function updateLead($id=0){
        $this->result = $this->connect('crm.lead.update', [
            'id'=>$id,
            'fields' =>$this->bitrix_data,
            'params' => [
                'REGISTER_SONET_EVENT' => 'Y'
            ],
        ]);
        return $this->getResponse();
    }

    public function addLead(){
        $this->result = $this->connect('crm.lead.add', [
            'fields' => $this->bitrix_data,
            'params' => [
                'REGISTER_SONET_EVENT' => 'Y'
            ],
        ]);
        return $this->getResponse();
    }
    public function getStatusList(){
        $this->result = $this->connect('crm.status.list',[
            'order'=> ["SORT"=> "ASC"],
            'filter'=> ["ENTITY_ID"=> "STATUS"]
        ]);
        return $this->getResponse();
    }
    public function getFieldsList(){
        $this->result = $this->connect('crm.lead.fields',array());
        return $this->getResponse();
    }
    public function getLead($leadId){
        $this->result = $this->connect('crm.lead.get', ['id' =>  $leadId]);
        return $this->getResponse();
    }
    public function getLeadStatus($leadId){
        $dealData = $this->getLead($leadId);
        $s = Status::where('index',$dealData['result']['STATUS_ID'])->first();
        return $s->id;
    }

    public function syncStatuses(){
        $dealData = $this->getStatusList();
        foreach ($dealData['result'] as $key=>$status){
            Status::where('id',$key+1)->update([
                'index' =>$status['STATUS_ID'],
                'ID_on_bitrix' =>$status['ID'],
                'name' =>  $status['NAME'],
                'color' => $status['COLOR']
            ]);
            if(Status::find($key+1)===null){
                Status::create([
                    'index' =>$status['STATUS_ID'],
                    'ID_on_bitrix' =>$status['ID'],
                    'name' =>  $status['NAME'],
                    'color' => $status['COLOR']
                ]);
            }
            //dump($status);
        }
        return true;

    }
    private function crmStatusAdd(){
        $this->connect('crm.status.add',[
            'fields' => $this->status
        ]);
    }

    protected function getError($response)
    {
        if ($this->debug) {
            if (!isset($response[1])) {
                $this->error = false;
                $response = $response[0];
            } else {
                $response = "Ошибка №" . $response[1] . "\n";
            }
        }
        return $response;
    }

    protected function sendCmd($cmd, $arg = '', $post = true)
    {

    }
    public function getB64Type($str) {
        // $str should start with 'data:' (= 5 characters long!)
        return substr($str, 5, strpos($str, ';')-5);
    }
    public function addDeal($vendor, $model,$year,$phone,$folder_name,$status='NEW'){

        $img = [];
        if(\File::exists('uploads/'.$folder_name)) {
            $filesInFolder = \File::files('uploads/'.$folder_name);
            foreach($filesInFolder as $path) {
                $file = pathinfo($path);
                $img[] =  $file['basename'] ;
            }
        }

        $data_img=[];
////
        foreach ($img as $key => $i){
            $data_img[] =
                curl_file_create(public_path('uploads').'/'.$folder_name.'/'.$img[$key],'image/*',$img[$key]);
        }



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

            $this->bitrix_data[self::crm_pics_field_name[$key]] = ['fileData'=>[
                $key.'.'.$file_ext,$data[ 1 ]
            ]];
        }

        $v = isset($vendor)==true ? $vendor :"";
        $car = isset($model)==true ? $model :"";

        if(env('BITRIX_HEADER')=='test'){
            $this->bitrix_data['TITLE'] = "Тестовое";
        }
        if(env('BITRIX_HEADER')=='real'){
            $this->bitrix_data['TITLE'] =  $v.' '.$car;
        }

        $this->bitrix_data['UF_CRM_1633361973449'] = $v.' '.$car; //real   "ID" => "312"

        $this->bitrix_data['UF_CRM_1633362091686'] =  isset($year)==true ? [self::years[$year]] :""; //real

        $this->bitrix_data['SOURCE_ID'] = "1";
        $this->bitrix_data['STATUS_ID'] = $status;
        $phone ?: $this->bitrix_data['PHONE'] =  [['VALUE' => $phone, 'VALUE_TYPE' => 'WORK']];
    }
}
