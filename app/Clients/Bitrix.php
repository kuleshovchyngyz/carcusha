<?php

namespace App\Clients;

use App\Models\Status;
use Illuminate\Support\Facades\Http;

class Bitrix
{
    const years = ["other" => "987", "2008" => "330", "2009" => "328", "2010" => "326", "2011" => "324", "2012" => "322", "2013" => "320", "2014" => "318", "2015" => "304", "2016" => "316", "2017" => "302", "2018" => "314", "2019" => "300", "2020" => "312", "2021" => "1085", "2022" => "1087"];
    const  crm_pics_field_name = ['UF_CRM_1526660196', 'UF_CRM_1541502441', 'UF_CRM_1541502457', 'UF_CRM_1541502472', 'UF_CRM_1541502488','UF_CRM_1548236628101'];//real
    const crm_pics_field= array(
            "Год выпуска"=>"UF_CRM_1526659328",
            "Модель"=>"UF_CRM_1526660063",
        );
    protected $http;
    protected $debug;
    protected $error = true;
    protected $result;
    protected $bitrix_data = [];

    public function __construct()
    {
        $this->http = env('BITRIX_CARCUSHA_WEBHOOK_URL','https://rosgroup.bitrix24.ru/rest/52/t6jwgqobgwc1rqyz/');
        $this->result = array();
    }

    public function connect($method, $data)
    {
        $res = Http::timeout(10)->post($this->http . $method, $data);
        $this->result = json_decode($res->body(), 1);
        return $this->result;

    }

    public function getResponse()
    {
        if (!array_key_exists('error_description', $this->result)) {
            return $this->result;
        }
        return false;
    }

    public function updateLead($id = 0)
    {
        $this->result = $this->connect('crm.lead.update', [
            'id' => $id,
            'fields' => $this->bitrix_data,
            'params' => [
                'REGISTER_SONET_EVENT' => 'Y'
            ],
        ]);
        return $this->getResponse();
    }

    public function addLead()
    {
        $this->result = $this->connect('crm.lead.add', [
            'fields' => $this->bitrix_data,
            'params' => [
                'REGISTER_SONET_EVENT' => 'Y'
            ],
        ]);
        return $this->getResponse();
    }

    public function getStatusList()
    {


        $this->result = $this->connect('crm.status.list', [
            'order' => ["SORT" => "ASC"],
            'filter' => ["ENTITY_ID" => "STATUS"]
        ]);
        return $this->getResponse();
    }

    public function getFieldsList()
    {
        $this->result = $this->connect('crm.lead.fields', array());
        return $this->getResponse();
    }

    public function getLead($leadId)
    {
        $this->result = $this->connect('crm.lead.get', ['id' => $leadId]);
        return $this->getResponse();
    }

    public function getLeadStatus($leadId)
    {
        $dealData = $this->getLead($leadId);
        $s = Status::where('index', $dealData['result']['STATUS_ID'])->first();
        return $s->id;
    }

    public function syncStatuses()
    {
        $dealData = $this->getStatusList();
        foreach ($dealData['result'] as $key => $status) {
            Status::where('index', $status['STATUS_ID'])->update([
                'index' => $status['STATUS_ID'],
                'ID_on_bitrix' => $status['ID'],
                'name' => $status['NAME'],
                'color' => $status['COLOR'],
                'updated'=>now()->format('Y.m.d')
            ]);
            if (Status::where('index', $status['STATUS_ID'])->count()==0) {
                Status::create([
                    'index' => $status['STATUS_ID'],
                    'ID_on_bitrix' => $status['ID'],
                    'name' => $status['NAME'],
                    'color' => $status['COLOR']
                ]);
            }
        }
        return true;

    }

    private function crmStatusAdd()
    {
        $this->connect('crm.status.add', [
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

    public function getB64Type($str)
    {
        // $str should start with 'data:' (= 5 characters long!)
        return substr($str, 5, strpos($str, ';') - 5);
    }

    public function addDeal($vendor, $model, $year, $phone, $folder_name, $status = 'NEW',$title='')
    {

//        dd($phone);
        $img = [];
        if (\File::exists('uploads/' . $folder_name)) {
            $filesInFolder = \File::files('uploads/' . $folder_name);
            foreach ($filesInFolder as $path) {
                $file = pathinfo($path);
                $img[] = $file['basename'];
            }
        }

        $data_img = [];
////
        foreach ($img as $key => $i) {
            $data_img[] =
                curl_file_create(public_path('uploads') . '/' . $folder_name . '/' . $img[$key], 'image/*', $img[$key]);
        }


        $arFiles = [];
        foreach ($data_img as $key => $ready) {
            $file_type = $this->getB64Type(file_get_contents($data_img[$key]->name));

            switch ($file_type) {
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
            $data = explode(',', file_get_contents($data_img[$key]->name));
            if ($key < 5) {
                $this->bitrix_data[self::crm_pics_field_name[$key]] = ['fileData' => [
                    $key . '.' . $file_ext, $data[1]
                ]];
            } else {
                $arFiles[] = ['fileData' => [
                    $key . '.' . $file_ext, $data[1]
                ]];
            }

        }
        $this->bitrix_data["UF_CRM_1548236628101"] = $arFiles;

        $v = isset($vendor) == true ? $vendor : "";
        $car = isset($model) == true ? $model : "";

        if (env('BITRIX_HEADER') == 'test') {
            $this->bitrix_data['TITLE'] = "Тестовое";
        }
        if (env('BITRIX_HEADER') == 'real') {
            $this->bitrix_data['TITLE'] = $v . ' ' . $car;
        }
        if ($title != '') {
            $this->bitrix_data['TITLE'] = $title;
        }

        $this->bitrix_data[self::crm_pics_field['Модель']] = $v . ' ' . $car; //real   "ID" => "312"

        $this->bitrix_data[self::crm_pics_field['Год выпуска']] = isset($year) == true ? [self::years[$year]] : ""; //real

        $this->bitrix_data['SOURCE_ID'] = "5";
        $this->bitrix_data['STATUS_ID'] = $status;
        $this->bitrix_data['PHONE'] = [['VALUE' => $phone, 'VALUE_TYPE' => 'WORK']];

    }

}
