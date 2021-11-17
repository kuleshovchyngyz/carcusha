<?php

namespace App\Clients;

use Illuminate\Support\Facades\Http;

class SmsClient
{
    protected $http;
    protected $login;
    protected $password;
    protected $charset;
    protected $debug;
    protected $error = true;

    public function __construct()
    {
        $this->http = Http::baseUrl(config('services.sms.main_url'))
            ->withHeaders([
                'Content-Type' => 'application/json'
            ]);
        $this->login = config('services.sms.login');
        $this->password = config('services.sms.password');
        $this->charset = config('services.sms.charset');
        $this->debug = config('services.sms.debug');
    }

    public function sendSms($phones, $message, $translit = 0, $time = 0, $id = 0, $format = 0, $sender = false, $query = "", $files = [])
    {
        static $formats = [1 => "flash=1", "push=1", "hlr=1", "bin=1", "bin=2", "ping=1", "mms=1", "mail=1", "call=1", "viber=1", "soc=1"];
        $url = "cost=3&phones=".urlencode($phones)
            ."&mes=".urlencode($message)."&translit=$translit&id=$id"
            .($format > 0 ? "&".$formats[$format] : "")
            .($sender === false ? "" : "&sender=".urlencode($sender))
            .($time ? "&time=".urlencode($time) : "").($query ? "&$query" : "");
        $result = false;
        $balance = $this->getBalance();
        if($balance > 0){
            $response = $this->sendCmd("send", $url);
            if($response != ''){
                $response = explode(',', $response);
                $result = (isset($response[0]) && $response[0] > 0) ? true : false;
            }
        }
        return $result;
    }

    public function getBalance()
    {
        return $this->sendCmd("balance", "", false);
    }

    protected function getError($response)
    {
        if($this->debug) {
            if(!isset($response[1])){
                $this->error = false;
                $response = $response[0];
            }else{
                $response = "Ошибка №".$response[1]."\n";
            }
        }
        return $response;
    }

    protected function sendCmd($cmd, $arg = '', $post = true)
    {
        $url = "/sys/$cmd.php?login=".urlencode($this->login)."&psw=".urlencode($this->password)."&fmt=1&charset=".$this->charset.(($arg != '') ? "&".$arg : '');
        return ($post)
            ? $this->http->post($url, [])->body()
            : $this->http->get($url)->body();
    }
}
