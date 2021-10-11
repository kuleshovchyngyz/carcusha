<?php

namespace App\support\Leads;

use App\Models\balance;
use App\Models\Payment;
use App\Models\PaymentAmount;
use App\Models\PendingAmount;
use App\Models\Reason;

class Pay
{
    protected $new_status,  $user, $lead, $amount, $amount_id, $reasons, $percent, $type, $user_who_referred,$percentAmount;
    public function __construct($lead,$new_status){
        $this->lead = $lead;
        $this->user = $this->lead->user;
        $this->user_who_referred = $this->user->user_who_referred();
        $this->new_status = $new_status;
        $this->reasons =  Reason::where('reason_name','lead')->where('table_id', $this->lead->bitrix_user_id)->get();
        $this->reasons_refer = Reason::where('reason_name','refer')->where('table_id', $this->user->id)->get();
        $this->new_status = $new_status;
        $this->amount = $this->new_status->user_statuses->amount;
        $this->percent = PaymentAmount::where('reason_of_payment','percentage')->first()->amount;
        if($this->new_status->id==14){
            $this->type = 'success';
            $this->amount_id = 2;
        }else{
            $this->type = 'others';
            if($this->amount > 0 ){
                $this->amount_id = 1;
            }
            if($this->amount<0){
                $this->amount_id = 5;
            }
        }
        $this->create_payments();
    }
    public function create_payments(){

        switch ($this->type) {
            case 'success':
                $this->create_success_deal_payment();
                break;
            default:
                $this->create_other_deal_payment();
                break;
        }
    }
    public function create_success_deal_payment(){

        if(!$this->check_for_lead_payments_success()) {
            if($this->check_for_lead_payments_for_referred_user()){
                $pending_amount = $this->pay_for_referred_user();
                $pending_amount->status = 1;
                $pending_amount->save();
            }
            $r_id = $this->create_reason('lead');
            $id = $this->create_payment($r_id, 'success');
            $this->create_pending_amount($id, 1);
            $this->defroze_initial_lead_payment();
            $this->defroze_percentage_referral_payments_for_referred_user();
            $this->change_balance();
        }
    }
    public function create_other_deal_payment(){

        if(!$this->check_for_lead_payments_initial() && $this->type!='success'){
            if( $this->amount > 0 ){
                $r_id = $this->create_reason( 'lead');
                $id = $this->create_payment($r_id, 'initial');
                $this->create_pending_amount($id, 0);
                $this->change_balance();
            }
        }
        if( $this->amount < 0 ){
            $this->defroze_initial_lead_payment();
            $r_id = $this->create_reason( 'lead');
            $id = $this->create_payment($r_id, 'rejected');
            $this->change_balance();
        }
    }
    public function check_for_lead_payments_for_referred_user(){
        if($this->user->user_who_referred()==false){
            return false;
        }

        foreach ($this->reasons_refer as $reason){
            $p = \App\Models\Payment::where('reason_id',$reason->id)->first();
            if($p){
                if($p->amount==3){
                    return true;
                }
            }
        }
        return false;
    }
    public function check_for_lead_payments_initial(){

        foreach ($this->reasons as $reason){
            $p = \App\Models\Payment::where('reason_id',$reason->id)->first();
            if($p){
                if($p->amount==1){
                    return true;
                }
            }
        }
        return false;
    }
    public function create_payment($reason_id,$type){
        $refer = '';
        if($type=='percent'){
            $user_id = $this->user->user_who_referred()->id;
            $amount = 6;
            $refer = 'refer';
            $paymentAmount = PaymentAmount::all();
            $referAmount = round((($paymentAmount[0]->amount + $paymentAmount[1]->amount) * $paymentAmount[5]->amount) / 100);
            $balance =$this->user->user_who_referred()->balance;
            $balance->balance = $balance->balance + $referAmount;
            $balance->save();

        }else{
            $user_id = $this->user->id;
        }

        $payment = \App\Models\Payment::create([
            'user_id' => $user_id,
            'reason_id'=>$reason_id,
            'amount'=> $refer == '' ? $this->amount_id : $amount,
            'status'=>0,
            'status_group'=> $refer =='' ?  $type.$this->new_status->index : $refer
        ]);


        return $payment->id;
    }
    public function create_pending_amount($payment_id,$status){
        return PendingAmount::create([
            'payment_id'=>$payment_id,
            'status'=>$status
        ]);
    }
    public function create_reason($reason){
        if($reason=='percent'){
            $reason = Reason::create([
                'table_id'=>$this->user->id,
                'reason_name'=> $reason,
                'user_id_who_referred'=>$this->user->user_who_referred()->id
            ]);
            return $reason->id;
        }
        $reason = Reason::create([
            'table_id'=>$this->lead->bitrix_user_id,
            'reason_name'=> $reason
        ]);
        return $reason->id;
    }
    public function defroze_initial_lead_payment(){
        if($this->check_for_lead_payments_initial()){
            $reasons =  Reason::where('reason_name','lead')->where('table_id', $this->lead->bitrix_user_id)->get();

            foreach ($reasons as $reason){
                $payment = \App\Models\Payment::where('reason_id',$reason->id)->first();
                if($payment->amount==1){
                    $pending_amount = $payment->pending_amount;
                    $pending_amount->status = 1;
                    $pending_amount->save();
                    return true;
                }
            }
        }
        return false;
    }
    public function pay_for_referred_user(){
        if($this->user->user_who_referred()==false){
        }else{

            if($this->check_for_lead_payments_for_referred_user())
            {

                $reason_id = $this->create_reason('percent');
                $id = $this->create_payment($reason_id,'percent');
                $pending_amount = $this->create_pending_amount($id,0);

                $balance = balance::where('user_id',$this->user->user_who_referred()->id)->first();
                $balance->balance = $balance->balance +  $this->amount*$this->percent/100;
                return $pending_amount;
            }

        }
    }
    public function defroze_percentage_referral_payments_for_referred_user(){
        if($this->check_for_lead_payments_for_referred_user()){

            $reasons =  Reason::where('reason_name','percent')->where('table_id', $this->lead->bitrix_user_id)->where('user_id_who_referred',$this->user->user_who_referred()->id)->get();
            foreach ($reasons as $reason){
                $payment = \App\Models\Payment::where('reason_id',$reason->id)->first();
                if($payment->amount==6){
                    $pending_amount = $payment->pending_amount;
                    $pending_amount->status = 1;
                    $pending_amount->save();
                    return true;
                }
            }
            $reasons =  Reason::where('reason_name','refer')->where('table_id', $this->user->id)->where('user_id_who_referred',$this->user->user_who_referred()->id)->get();
            foreach ($reasons as $reason){
                $payment = Payment::where('reason_id',$reason->id)->first();
                if($payment->amount==6 || $payment->amount==3){
                    $pending_amount = $payment->pending_amount;
                    $pending_amount->status = 1;
                    $pending_amount->save();
                    return true;
                }
            }
        }
        return false;
    }
    public function change_balance(){
        $balance = $this->user->balance;
        $balance->balance = $balance->balance + $this->amount;
        $balance->save();
    }
    public function check_for_lead_payments_success(){


        foreach ($this->reasons as $reason){
            $p = \App\Models\Payment::where('reason_id',$reason->id)->first();
            if($p){
                if($p->amount==2){
                    return true;
                }
            }
        }
        return false;
    }
}
