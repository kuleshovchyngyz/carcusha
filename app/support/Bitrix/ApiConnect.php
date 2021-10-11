<?php

namespace App\support\Bitrix;

use Illuminate\Support\Facades\Http;

class ApiConnect
{
    const webhook_url = "https://rosgroup.bitrix24.ru/rest/52/tvk30z03175k7x2p/";
    const fields = [
          'isInAvito.ru' => 'UF_CRM_1526732995',
          'isInAuto.ru' => 'UF_CRM_1526733011',
        ];
    private $method, $data;
    public $result;
    public function __construct ($method,$data)
    {
        $this->method = $method;
        $this->data = $data;
        $this->execute();

    }
    public function execute ()
    {
        $res = Http::timeout(5)->post(self::webhook_url.$this->method ,$this->data);
        $this->result = json_decode($res->body(), 1);
    }
    public function getResponse(){
        if(!array_key_exists('error_description',$this->result)){
            return $this->result;
        }
        return false;
    }
    public function getResult()
    {
        return $this->result['result'];
    }
    public function getFieldName($fieldName){
        if(!array_key_exists($fieldName,self::fields)){
            return $this->result['result'][$fieldName];
        }
        return $this->result['result'][self::fields[$fieldName]];
    }

}
