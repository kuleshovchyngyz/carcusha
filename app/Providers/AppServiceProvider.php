<?php

namespace App\Providers;

use App\Models\Major;
use App\Models\User;
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
        Validator::extend('email_format', function($attribute, $value, $parameters)
        {
            if(filter_var($value , FILTER_VALIDATE_EMAIL)){
                return $value;
            }

        });
        Validator::extend('is_email_in_database', function($attribute, $value, $parameters)
        {
            if (User::where('email', '=', $value)->count() > 0) {
                return $value;
            }


        });
        Validator::extend('is_number_in_database', function($attribute, $value, $parameters)
        {
            if (User::where('number', '=', $value)->count() > 0) {
                return $value;
            }

        });
        Validator::extend('is_promocode_in_database', function($attribute, $value, $parameters)
        {
//           return $value;

            if (User::where('invitation_code', '=', $value)->count() > 0 ) {
                return $value;
            }

        });
        Validator::extend('not_empty', function($attribute, $value, $parameters)
        {
            if ( $value != '') {
                return $value;
            }

        });
        Validator::extend('confirm_email_settings', function($attribute, $value, $parameters)
        {
            if (User::where('email', '=', $value)->where('id','!=',auth()->user()->id)->count() == 0) {
                return $value;
            }
        });
        Validator::extend('confirm_phone_settings', function($attribute, $value, $parameters)
        {
            if (User::where('number', '=', $value)->where('id','!=',auth()->user()->id)->count() == 0) {
                return $value;
            }
        });
        Validator::extend('required_major', function($attribute, $value, $parameters)
        {
            $majors = Major::pluck('name')->toArray();
            if ($value!==null&&in_array($value, $majors)) {
                return $value;
            }
        });
    }
}
