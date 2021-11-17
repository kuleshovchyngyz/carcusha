<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserStatuses extends Model
{
    use HasFactory;
    protected $fillable = ['status_id','name','amount','notify','payment_amount_id']
    ;
    public function amount($user=null){
        if($user&&$user->unique_payment){
            return UserPaymentAmount::where('user_id',$user->id)->where('reason_of_payment',$this->attributes['amount'])->first()->amount;
        }
        return PaymentAmount::where('reason_of_payment',$this->attributes['amount'])->first()->amount;
    }
}
