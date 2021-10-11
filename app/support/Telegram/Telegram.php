<?php

namespace App\support\Telegram;

use Illuminate\Support\Facades\Http;

class Telegram
{
    protected static  $url = 'https://t.kuleshov.studio/api/getmessages';
    protected $data;

    public function send_to_tg_($message,$from)
    {
        $webhook_url = "https://rosgroup.bitrix24.ru/rest/52/tvk30z03175k7x2p/";//real
        $res = Http::timeout(5)->post($webhook_url.$method ,$data);
    }

    public function send_to_tg_bot($message){

        $message1 = $message ;
        $data = ["companycode" => "coe5c447c2a168d", "data" => [["message" => $message1]]];
        $url = 'https://t.kuleshov.studio/api/getmessages';

        $data_string = json_encode($data);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        return true;
    }
}
