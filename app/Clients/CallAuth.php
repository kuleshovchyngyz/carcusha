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
    public function call1($number,$pin){
        $data = json_encode([
            //   'callerId' => '79111897638',
            'dstNumber' => $number,
            //   'srcNumber' => '996708277186',
            'timeout' => 30,
            "pin"=> $pin
            // 'callDetails'=>[
            //       "callId"=> "2096093321622464437",
            //       "pin"=> "1234"
            // ]
        ]);

        $time = time();
        $resId = curl_init();
        $key = $this->getKey('call-password/start-password-call',
            $time,'d873b55e666537e839b8c892d2565a47985a360e278c7804',
            $data,'825b89ddb65a590608ee96d2e4f973ad762d94fad9b800a1');
        curl_setopt_array($resId, [
            CURLINFO_HEADER_OUT => true,
            CURLOPT_HEADER => 0,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer '.$key ,
                'Content-Type: application/json' ,
            ],
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_URL => 'https://api.new-tel.net/call-password/start-password-call',
            CURLOPT_POSTFIELDS => $data,
        ]);
        $response = curl_exec($resId);
        $curlInfo = curl_getinfo($resId);
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
               'message' =>  "SKYvin.ru, ?????? ??????: ".$pin
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
