<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

trait MessageNotificationTrait
{
    public static function boot()
    {
        parent::boot();

        static::retrieved(function ($item) {

        });
        static::saving(function ($item) {

        });
        static::updated(function ($item) {

        });
        static::deleted(function ($item) {
//            Log::info('Deleted   event call: ' . $item);
        });
        static::created(function ($item) {
            $message = ['to'=> $item->user->firebase_token, 'notification'=>["title"=>$item->lead->vendor.' '.$item->lead->vendor_model.', '. $item->lead->vendor_year, "body"=> $item->message]];
                        Log::info('notification ' . collect($message));
            $res = Http::withHeaders(['Authorization'=>env('FIREBASE_TOKEN')])->post(env('FIREBASE_URL') ,$message);
        });
    }
}
