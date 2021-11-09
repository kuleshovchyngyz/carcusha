<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentSetting extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','fullName','passportNumber','bankName','bik','inn','rs','ks','cardNumber'];
}
