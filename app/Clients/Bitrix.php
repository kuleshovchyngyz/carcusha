<?php

namespace App\Clients;
use App\Models\Status;
use Illuminate\Support\Facades\Http;

class Bitrix
{
    protected $http;
    protected $debug;
    protected $error = true;
    protected $result;
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

    public function addLeadAdd($array = []){
        $this->result = $this->connect('crm.lead.add', [
            'fields' => $array,
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
}
