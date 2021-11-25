<?php

namespace App\Models;

use App\support\Bitrix\ApiConnect;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use phpDocumentor\Reflection\Types\True_;
use App\Models\Notification;

class Lead extends Model
{
    use HasFactory;
    protected $table = 'leads';
    protected $fillable = [
        'number',
        'phonenumber',
        'car_name',
        'is_active',
        'vendor',
        'vendor_model',
        'vendor_year',
        'user_id',
        'bitrix_user_id',
        'folder',
        'status_id',
        'checked',
        'checked_texts'
    ];


    public function status()
    {
        $status = Status::find($this->attributes['status_id']);
        if($status){
            return $status;
        }
        return false;
    }
    public function payment_by_status(){
//        dump( $this->status()->type());
//        $p = PaymentAmount::where('reason_of_payment',$this->status()==false ? 0 : $this->status()->type())->first();
        return  $this->status()==false ? 0 : $this->status()->user_statuses->amount();

//        return $p->amount;
    }
    public function leads_total_payment(){

    }
    public function history(){
        $n = Notification::where('lead_id',$this->attributes['bitrix_user_id'])->orderBy('created_at','DESC')->get();
        if($n->count()>1){
            return $n[1];
        }
        else {
            return false;
        }
        return $n;
    }

    public function is_on_pending(){
        $reasons = Reason::wheretable_id($this->attributes['bitrix_user_id'])->wherereason_name('lead')->get();
        foreach ($reasons as $reason){
            $payment = Payment::wherereason_id($reason->id)->first();
            if ($payment){
                if($payment->amount==2 ){
                    return true;
                }
            }
        }
        return false;
    }
    public function all_amount(){
        $reasons = Reason::wheretable_id($this->attributes['bitrix_user_id'])->wherereason_name('lead')->get();
        $payment_amounts = [];
        $sum = 0;
        foreach ($reasons as $reason){
            $payment = Payment::wherereason_id($reason->id)->first();
            if ($payment){
                $sum += $payment->payment_amount()->amount;
            }
        }
        return $sum;

    }

    public function checked()
    {
        if( $this->attributes['checked'] )
        {
            return false;
        }
        return $this->attributes['checked_texts'];
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }


}
