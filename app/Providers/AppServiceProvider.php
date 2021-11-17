<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('phone_number', function($attribute, $value, $parameters)
        {
            $pattern_start = '/^[+]{0,1}[0-9\s]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/';
            if (preg_match($pattern_start, $value)&&strlen($value)>15){
                return $value;
            }

        });
//        Validator::extend('verification_code', function($attribute, $value, $parameters)
//        {
//            $pattern_start = '/^[+]{0,1}[0-9\s]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/';
//            if (preg_match($pattern_start, $value)&&strlen($value)>15){
//                return $value;
//            }
//
//        });
    }
}
