<?php

namespace App\Clients;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Http;

class TelegramBot
{
    protected  $telegramWebhook;
    protected $text;
    protected $user;
    protected $telegramId;
    protected $data;
    protected $companyCode;
    public function __construct($text,$user){
        $this->telegramWebhook =  env('TELEGRAM_WEB_HOOK','https://t.kuleshov.studio/api/getmessages');
        $this->text = $text;
        $this->user = $user;
        $this->telegramId = ($user->setting->telegram_id ===null) ? false : $user->setting->telegram_id;
        $this->companyCode =  SiteSetting::where('name','telegramBotToken')->first();

        if($this->telegramId!==false && $this->companyCode!=null){
            $this->companyCode = $this->companyCode->value;
            $this->makeTextReady();
            $this->sendMessage();
        }

    }
    public function makeTextReady(){
        $this->data = ["companycode" => $this->companyCode, "data" => [["message" =>  $this->text,"userId"=>$this->telegramId]]];
    }
    public function sendMessage(){
        try {
            $res = Http::post($this->telegramWebhook,$this->data);
            return $res->object();
        }  catch (\Throwable $e) {
            echo "Telegram notification not worked!";
        }
    }
}
