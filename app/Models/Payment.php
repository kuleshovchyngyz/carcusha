<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','reason_id','amount','status','status_group'];
    public function reasons(){
        return $this->hasOne(Reason::class ,'id','reason_id');
    }
    public function status(){
        //dd($this->attributes['status_group']);
        if(str_contains($this->attributes['status_group'],'initial')){
            return Status::where('index', str_replace("initial","",$this->attributes['status_group']))->first();
        }
        else {
            return false;
        }
    }
    public function payment_amount(){
        return $this->attributes['amount'];
        if($this->owner()->unique_payment){
            return UserPaymentAmount::where('user_id',$this->attributes['user_id'])->where('reason_of_payment',$this->paymentAmount->reason_of_payment)->first();
        }
        return $this->paymentAmount;
    }
    public function paymentAmount(){
//        return $this->attributes['amount'];
        return $this->belongsTo(PaymentAmount::class,'amount');
    }
//    public function amount(){
////        $pa = PaymentAmount::all()->pluck('reason_of_payment','id');
////        foreach ($pa as $key => $paymentamount){
////            if(str_contains($this->attributes['status_group'],$paymentamount)){
////                return PaymentAmount::find($key)->amount;
////            }
////        }
//        return $this->payment_amount()->amount;
//
//
//    }
    public function owner(){
        return User::find($this->attributes['user_id']);
    }
    public function referred_user(){

        //$refer = Refer::where('user_id',$this->attributes['user_id'])->first


        return User::find($this->reasons->table_id);
    }
    public function pending_amount(){
        return $this->hasOne(PendingAmount::class);
    }
    public function refer_amount(){
        $paymentAmount = PaymentAmount::all();
        return round((($paymentAmount[0]->amount + $paymentAmount[1]->amount) * $paymentAmount[5]->amount) / 100) ;
    }
}



