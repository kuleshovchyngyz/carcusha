<?php

namespace App\Clients;

use Illuminate\Support\Facades\Http;

class SmsClient
{
    public function sms($number,$pin){
        $data = json_encode([
           "security"=> [ "apiKey"=> env('AUTH_API_KEY')],
           'number' => $number,
           'capacity'=>'4',
           "flashcall"=> ["code"=> $pin]  ,
           ]);
           
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
           // print($outData);
      }
}
