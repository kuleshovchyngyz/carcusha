<?php

namespace App\support\Bitrix;

use Illuminate\Support\Facades\Http;

class ApiConnect

{
    protected $webhook_url;
    const crmLeadList = 'crm.lead.list';
    const crmLeadListData = ['order' => ['STATUS_ID'=> 'ASC' ] , 'select'=> [ "ID", "TITLE", "STATUS_ID", "OPPORTUNITY", "CURRENCY_ID" ]];
    const fields = [
          'isInAvito.ru' => 'UF_CRM_1526732622',
          'isInAuto.ru' => 'UF_CRM_1526733011',
        ];
    private $method, $data;
    public $result;
    public function __construct ($method="",$data=[])
    {
        $this->method = $method;
        $this->webhook_url = env('BITRIX_CARCUSHA_WEBHOOK_URL');
        $this->data = $data;
        if($method!="")
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
    public function setMethod($method)
    {
        $this->method = $method;
    }
    public function setData($data)
    {
        $this->data = $data;
    }
    public function getLeadList(){
        $this->setMethod(self::crmLeadList);
        $this->setData(self::crmLeadListData);
        $this->execute();
        return $this->getResponse();
    }


}
