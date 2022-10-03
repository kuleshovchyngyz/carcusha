<?php

namespace App\Clients;

use Illuminate\Support\Facades\Http;

class CallAuth
{
    public function call($number){
      $data = json_encode([
         "security"=> [ "apiKey"=> env('AUTH_API_KEY')],
         'number' => $number,
          "voice"=> []
         ]);
        //  dd($data);
         $url = env('CALL_URL');
         $apiKey = env('AUTH_API_KEY');
         // var_dump($data);
         $ch = curl_init();
         curl_setopt($ch, CURLOPT_URL, $url);
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
         curl_setopt($ch, CURLOPT_HEADER, FALSE);
         curl_setopt($ch, CURLOPT_POST, TRUE);
         curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
         curl_setopt($ch, CURLOPT_HTTPHEADER, array(
          "Content-Type: application/json",
          "Authorization: $apiKey"
         ));
         $outData = curl_exec($ch);
         curl_close($ch);
         return json_decode($outData,true)['code'];
         // print($outData);
    }

    public function getKey ($methodName , $time , $keyNewtel , $params , $writeKey)
    {
        return $keyNewtel.$time.hash( 'sha256' ,
                $methodName."\n".$time."\n".$keyNewtel."\n".
                $params."\n".$writeKey);
    }
    public function sms($number,$pin){



         $data = json_encode([
               'numbers' => [$number],
               'message' =>  "SKYvin.ru, Ваш код: ".$pin
               ]);
               // dd($data);
               $url = env('SMS_URL');
               $apiKey = env('AUTH_API_KEY');
               $ch = curl_init();
               curl_setopt($ch, CURLOPT_URL, $url);
               curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
               curl_setopt($ch, CURLOPT_HEADER, FALSE);
               curl_setopt($ch, CURLOPT_POST, TRUE);
               curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
               curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Content-Type: application/json",
                "Authorization: $apiKey"
               ));
               $outData = curl_exec($ch);
               curl_close($ch);
    }

}
