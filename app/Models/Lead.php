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
    use \Staudenmeir\EloquentHasManyDeep\HasRelationships;
    use \Znck\Eloquent\Traits\BelongsToThrough;
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
        'bitrix_lead_id',
        'folder',
        'status_id',
        'checked',
        'checked_texts'
    ];

    public function color(){
        if($this->status->color=='#EB5757'){
            return 'statusRed';
        }
        if($this->status->color=='#27AE60'){
            return 'statusGreen';
        }
        if($this->status->color=='#2D9CDB'){
            return 'statusBlue';
        }

        return '';
    }
    public function status()
    {
        return $this->belongsTo(Status::class);
        $status = Status::find($this->attributes['status_id']);
        if($status){
            return $status;
        }
        return false;
    }
    public function payment_by_status(){
        return  $this->status==null ? 0 : $this->status->user_statuses->amount();
    }
    public function history(){
        $n = Notification::where('lead_id',$this->attributes['bitrix_lead_id'])->orderBy('created_at','DESC')->get();
        if($n->count()>1){
            return $n[1];
        }
        else {
            return false;
        }
        return $n;
    }
    public function leadHistory(){
        return $this->hasMany(Notification::class,'lead_id','bitrix_lead_id');
//        return Notification::where('lead_id',$this->attributes['bitrix_lead_id'])->orderBy('created_at','ASC')->get();
    }

    public function is_on_pending(){
        $reasons = Reason::wheretable_id($this->attributes['bitrix_lead_id'])->wherereason_name('lead')->pluck('id');
        $bought = Payment::whereIn('reason_id',$reasons)->where('status_group','like','success%')->get();
        return ($bought->count()==1) ? true : false;
    }
    public function all_amount(){
//        dump($this->attributes['bitrix_lead_id']);
        $reasons = Reason::wheretable_id($this->attributes['bitrix_lead_id'])->wherereason_name('lead')->get();
        $payment_amounts = [];
        $sum = 0;
        foreach ($reasons as $reason){
            $payment = Payment::wherereason_id($reason->id)->first();
            if ($payment){
//                $sum += $payment->payment_amount()->amount;
                $sum += $payment->amount;
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
//    public function leadsNotification(){
//        return $this->belongsToThrough(Notification::class, [User::class]);
//    }
    public function fantom()
    {
        return $this->hasOne(Fantom::class,"bitrix_lead_id","bitrix_lead_id");
    }

}
