<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email',
        'number',
        'invitation_code',
        'password',
        'phone_verified_at',
        'email_verified_at',
        'active',
        'unique_payment'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function new_leads_quantity(){
        $count = 0;
        $count = Lead::where('user_id',$this->attributes['id'])->where('checked',false)->count();
//        foreach ($this->leads as $lead){
//            if($lead->checked==0){
//                $count++;
//            }
//        }
        return $count;
    }
    public function UserPaymentAmounts(){
        return $this->hasMany(UserPaymentAmount::class);
    }
    public function new_leads(){
        $leads = Lead::whereuser_id($this->attributes['id'])->where('checked',0)->get();
        return $leads;
    }

    public function leads_reviewed(){
        Lead::where('user_id',$this->attributes['id'])->update(['checked'=>1]);
    }
    public function setting()
    {
        return $this->hasOne(Setting::class);
    }
    public function partners(){
        $users = Refer::whereuser_id($this->attributes['id'])->pluck('referred_user_id');
        return User::whereIn('id',$users)->get();
    }

    public function partner_payment(){

    }

    public function balance(){
        return $this->hasOne(balance::class);
    }


    public function user_who_referred(){

        $referred_user =   Refer::wherereferred_user_id($this->attributes['id'])->pluck('user_id');
        if(count($referred_user)>0){
            return User::find($referred_user[0]);
        }
        return false;
    }


    public function leads(){
        return $this->hasMany(Lead::class);
    }
    public function number_of_partner_leads(){
        $sum=0;
        foreach ($this->partners() as $partner){
            $sum = $sum + $partner->leads->count();
        }
        return $sum;
    }
    public function amount_of_referral(){
        $p= PaymentAmount::where('reason_of_payment','refer')->first();
        return $p->amount;
    }
    public function total_amount_from_referral(){
        $p= PaymentAmount::where('reason_of_payment','refer')->first();
        return $this->partners()->count()*$p->amount;
    }
    public function payments(){
        return $this->hasMany(Payment::class);
    }

    public function total_amount_from_all(){

        $sum=0;
        foreach ($this->payments as $payment){
            $sum = $sum + $payment->amount;
        }
        return $sum;
    }
    public function set_as_paid()
    {
        foreach ($this->payments as $payment){
           $payment->status = 1;
           $payment->save();
        }
    }
//    public function pay(Paid $paid)
//    {
////        $threshhold = PaymentAmount::where('reason_of_payment','MinAmountOfPayment')->first()->amount;
////        $paid = Paid::where('user_id',auth()->user()->id)->where('status','pending')->get();
//////        if($paid->count()>0){
//////
//////        }
////
////            Paid::create(['user_id'=>$this->attributes['id'],'amount'=>$this->total_amount_from_all()]);
////
////            $this->set_as_paid();
////
////        $paid->status = 'complete';
////        $paid->save();
////            return true;
//
//
//    }

    public function lead_payments(){
        $rs = Payment::where('user_id',$this->attributes['id'])->pluck('reason_id');
        $rlid = Reason::whereIn('id',$rs)->where('reason_name','lead')->pluck('table_id');

        $lead_real = Lead::whereIn('bitrix_user_id',$rlid)->pluck('bitrix_user_id');

        $intersect = $rlid->intersect($lead_real);

        $r = Reason::where('reason_name','lead')->whereIn('table_id',$intersect)->pluck('id');



        return Payment::where('user_id', $this->attributes['id'])->where('status_group','<>','refer')->whereIn('reason_id',$r)->get();
    }

    public function payments_by_refer()
    {
        return Payment::where('user_id', $this->attributes['id'])->where('status_group','=','refer')->get();
    }

    public function pending(){
        $count = 0;
        $pending = [20,2,30,31,9,23,32,33,36,'ASSIGNED','CANNOT_CONTACT'];
        foreach ($this->leads as $lead){
            if(in_array($lead->status_id,$pending)){
                $count++;
            }
        }
        return $count;
    }
    public function rejected(){
        $count = 0;
        $rejected = [1,26,13,4,10,29,24,34,25,35,37,'JUNK'];
        foreach ($this->leads as $lead){
            if(in_array($lead->status_id,$rejected)){
                $count++;
            }
        }
        return $count;
    }
    public function paid(){
        $paids = Paid::where('user_id',$this->attributes['id'])->where('status','complete')->pluck('amount');
        return $paids->sum();
    }

    public function sumOfPendingPaidAmount(){
        $paids = Paid::where('user_id',$this->attributes['id'])->where('status','pending')->pluck('amount');
        return $paids->sum();
    }
    public function availableAmount(){
       return $this->balance->balance - $this->SumOfPendingAmount() - $this->sumOfPendingPaidAmount();
    }

    public function number_of_violations(){
        return $this->violations->count();

    }


    public function payment(){
        return $this->hasMany(Payment::class);
    }
    public function violations(){
        return $this->hasMany(Violation::class);
    }

    public function status(){
        return $this->attributes['active'] == 1 ? 'Активный' : 'Блок';
    }

    public function paids(){
        return $this->hasMany(Paid::class);
    }
    public function sum_of_paids(){
        $sum = $this->paids->where('status','complete')->pluck('amount')->sum();
        return $sum;
    }
    public function notifier(){
        return $this->hasOne(Notifier::class);
    }

    public function last_paid_date(){
        if($this->paids->count()>0){
            return $this->paids->last()->created_at->format('d-m-Y H:i');
        }
        else return '--';
    }

    public function SumOfPendingAmount(){
        $payments = Payment::where('user_id',$this->attributes['id'])->pluck('id');
        $pending = PendingAmount::whereIn('payment_id',$payments)->where('status',false)->pluck('payment_id');
        $payment_amount = Payment::whereIn('id',$pending)->pluck('amount')->sum();
//        dd($payment_amount);
//        $sum = [];
//        foreach ($payment_amount as $p){
//            $sum[] = PaymentAmount::find($p)->amount;
//        }
        return $payment_amount;
    }

    public function paymentSetting(){
        return $this->hasOne(PaymentSetting::class);
    }
    public function promo(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Promo::class);
    }

}
