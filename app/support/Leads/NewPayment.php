<?php

namespace App\support\Leads;

use App\Models\Payment;
use App\Models\PaymentAmount;
use App\Models\PendingAmount;
use App\Models\Reason;

class NewPayment
{
    protected $new_status,  $user, $lead, $amount, $reasons, $percent, $typeOfStatus, $user_who_referred,$balance,$continue ;
    public function __construct($lead,$new_status)
    {
        echo 'in construct';
        $this->continue = true;
        $this->lead = $lead;
        $this->user = $this->lead->user;
        $this->balance = $this->user->balance;
        $this->user_who_referred = $this->user->user_who_referred();
        $this->new_status = $new_status;
        $this->reasons =  Reason::where('reason_name','lead')->where('table_id', $this->lead->bitrix_lead_id)->get();
        $this->reasons_refer = Reason::where('reason_name','refer')->where('table_id', $this->user->id)->get();
        $this->percent = PaymentAmount::where('reason_of_payment','percentage')->first()->amount;
        $this->typeOfStatus = $this->new_status->user_statuses->amount;
        $this->determineAmount();
        $this->createPayment();
    }
    protected function createPayment(){
        $this->payForUser()
            ->payForReferredUser()
            ->defroze_initial_lead_payment()
            ->defrozePaymentsOfUserWhoReferred()
            ->changeBalance()
        ;
    }

    protected function defrozePaymentsOfUserWhoReferred(){
        echo 'in defrozePaymentsOfUserWhoReferred';
        if(!$this->continue){
            return $this;
        }
        if($this->typeOfStatus=='success' && $this->user_who_referred !== false ){
            $this->user_who_referred->payments_by_refer();
            $reasonIds =  Reason::where('reason_name','refer')->where('table_id', $this->user->id)->where('user_id_who_referred',$this->user_who_referred->id)->pluck('id');
            $payment = Payment::whereIn('reason_id',$reasonIds)->where('user_id',$this->user_who_referred->id)->where('status_group','refer')->first();
            $pending_amount = $payment->pending_amount;
            $pending_amount->status = 1;
            $pending_amount->save();

        }
        return $this;
    }

    public function defroze_initial_lead_payment(){
        echo 'in defroze_initial_lead_payment';
        if(!$this->continue){
            return $this;
        }
        if($this->check_for_lead_payments_initial()){
            $reasonIds =  Reason::where('reason_name','lead')->where('table_id', $this->lead->bitrix_lead_id)->pluck('id');
            $payment = Payment::whereIn('reason_id',$reasonIds)->where('status_group','like','initial%')->first();
            $pending_amount = $payment->pending_amount;
            $pending_amount->status = 1;
            $pending_amount->save();
        }
        return $this;
    }
    public function check_for_lead_payments_initial(){

        foreach ($this->reasons as $reason){
            $p = \App\Models\Payment::where('reason_id',$reason->id)->where('status_group','like','%initial%')->count();
            if($p>0){
                    return true;
            }
        }
        return false;
    }

    public function payForUser(){
        echo 'in payForUser';
        if($this->typeOfStatus=='nothing'){
            $this->continue = false;
            return $this;
        }
        if($this->check_for_lead_payments_initial() && $this->typeOfStatus=='initial' ){
            $this->continue = false;
           return $this;
        }
        if($this->check_for_lead_payments_success() && $this->typeOfStatus=='success' ){
            $this->continue = false;
           return $this;
        }

            $reason = Reason::create([
                'table_id'=>$this->lead->bitrix_lead_id,
                'reason_name'=>'lead'
            ]);
            $payment = \App\Models\Payment::create([
                'user_id' => $this->user->id,
                'reason_id'=>$reason->id,
                'amount'=> $this->amount,
                'status'=>0,
                'status_group'=>$this->typeOfStatus.$this->new_status->index
            ]);
            $this->create_pending_amount($payment->id,($this->typeOfStatus=='success' || ($this->typeOfStatus=='rejected') ? 1 : 0 ));

        return $this;
    }
    public function create_pending_amount($payment_id,$status){
        return PendingAmount::create([
            'payment_id'=>$payment_id,
            'status'=>$status
        ]);
    }
    public function payForReferredUser(){
        echo 'in payForReferredUser';
        if(!$this->continue){
            return $this;
        }
        if($this->typeOfStatus=='success' && $this->user_who_referred!== false ){
            $reason = Reason::create([
                'table_id'=> $this->user->id,
                'reason_name'=>'percent',
                'user_id_who_referred'=> $this->user_who_referred->id
            ]);

            $referAmount = round(($this->lead->all_amount())*$this->percent/100);
            $payment = \App\Models\Payment::create([
                'user_id' => $this->user_who_referred->id,
                'reason_id'=> $reason->id,
                'amount'=> $referAmount,
                'status'=>0,
                'status_group'=>'refer'
            ]);
            $balance =$this->user_who_referred->balance;
            $balance->balance = $balance->balance + $referAmount;
            $balance->save();
            $this->create_pending_amount($payment->id,1);
        }
        return $this;
    }

    protected function changeBalance(){
        echo 'in changeBalance';
        if(!$this->continue){
            return $this;
        }
        $this->balance->balance = $this->balance->balance + $this->amount;
        $this->balance->save();
        return $this;
    }
    protected function determineAmount(){
        $this->amount = $this->new_status->user_statuses->amount($this->user);
        //dd($this->typeOfStatus);
        if( $this->user_who_referred!==false && $this->user->payments->where('status_group','successCONVERTED')->count()===0 && $this->typeOfStatus=='success'){
            $this->amount = PaymentAmount::where('reason_of_payment','firstPayment')->first()->amount;
        }
    }

    protected function check_for_lead_payments_success(){
        foreach ($this->reasons as $reason){
            $p = \App\Models\Payment::where('reason_id',$reason->id)->where('status_group','like','success%')->count();
            if($p>0){
                return true;
            }
        }
        return false;
    }

}
