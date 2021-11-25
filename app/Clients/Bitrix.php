<?php

namespace App\Clients;
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
        $res = Http::timeout(5)->post($this->http.$method ,$data);
        $this->result =  json_decode($res->body(), 1);
        return $this->result;
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
    public function getResponse(){
        if(!array_key_exists('error_description',$this->result)){
            return $this->result;
        }
        return false;
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
