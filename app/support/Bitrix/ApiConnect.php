<?php

namespace App\support\Bitrix;

use Illuminate\Support\Facades\Http;

class ApiConnect
{
    protected $webhook_url;
    const fields = [
          'isInAvito.ru' => 'UF_CRM_1633362412350',
          'isInAuto.ru' => 'UF_CRM_1633362418776',
        ];
    private $method, $data;
    public $result;
    public function __construct ($method,$data)
    {
        $this->method = $method;
        $this->webhook_url = env('BITRIX_CARCUSHA_WEBHOOK_URL');
        $this->data = $data;
        $this->execute();

    }
    public function execute ()
    {
        $res = Http::timeout(10)->post($this->webhook_url.$this->method ,$this->data);
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
