<?php

namespace App\support\Leads;

use App\Models\balance;
use App\Models\Lead;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\PendingAmount;
use App\Models\Reason;
use App\Models\Status;
use App\Models\User;

class LeadBuilder extends UpdatingLeadStatus
{
    public $vendor;
    public $vendor_model;
    public $vendor_year;
    public $phone;
    public $folder;
    public $user_id;
    public $bitrix_user_id;
    public $status;
    public $from;
    private $reason_id;
    private $reason;
    private $payment;

    public function __construct($vendor='', $vendor_model='', $vendor_year='', $phone='', $folder='', $user_id='', $bitrix_user_id='',$from = 0)
    {
        $this->vendor = $vendor;
        $this->vendor_model = $vendor_model;
        $this->vendor_year = $vendor_year;
        $this->phone = $phone;
        $this->folder = $folder;
        $this->user_id = $user_id;
        $this->user = User::find($user_id);
        $this->status = Status::where('name','Новый лид')->first();
        $this->bitrix_user_id = $bitrix_user_id;
        $this->from = $from;
        $this->create_lead();
        parent::__construct($bitrix_user_id,1);

//
//        $this->create_reason();
//        $this->create_payment();
//        $this->create_pending_amount();
//        $this->change_balance();
//        $this->notification();
    }
//    public function change_balance(){
//        $balance = $this->user->balance;
//        $balance->balance = $balance->balance + $this->status->user_statuses->amount() ;
//        $balance->save();
//    }
//    public function create_pending_amount(){
//        PendingAmount::create([
//            'payment_id'=>$this->payment->id,
//            'status'=>0
//        ]);
//    }
//    public function create_payment(){
//        $this->payment = Payment::create([
//            'user_id' => $this->user_id,
//            'reason_id'=>$this->reason->id,
//            'amount'=>1,
//            'status'=>0,
//            'status_group'=>'initialNEW'
//        ]);
//    }
//    public function create_reason(){
//        $this->reason = Reason::create([
//            'table_id'=>$this->lead->bitrix_user_id,
//            'reason_name'=> 'lead'
//        ]);
//    }
    public function create_lead()
    {
        Lead::create([
            'vendor' =>$this->vendor ,
            'vendor_model' => $this->vendor_model,
            'vendor_year' => $this->vendor_year,
            'phonenumber' => $this->phone,
            'folder' => $this->folder,
            'user_id' =>$this->user_id ,
            'bitrix_user_id' => $this->bitrix_user_id,
            'status_id' => 1,
            'number'=>$this->from
        ]);

    }

//    public function create_lead_payments_for_referred_user(){
//        if(!$this->check_for_lead_payments_for_referred_user()){
//            $reason_id = $this->create_reason('percent');
//            $id = $this->create_payment($reason_id,'percent');
//            $pending_amount = $this->create_pending_amount($id,0);
//
//            $balance = balance::where('user_id',$this->user->user_who_referred()->id)->first();
//            $balance->balance = $balance->balance +  $this->amount*$this->percent/100;
//            return $pending_amount;
//        }
//    }
//    public function check_for_lead_payments_for_referred_user(){
//        if($this->user->user_who_referred()==false){
//            return false;
//        }
//        $reasons =  Reason::where('reason_name','percent')->where('table_id', $this->lead->bitrix_user_id)->get();
//        foreach ($reasons as $reason){
//            $p = Payment::where('reason_id',$reason->id)->first();
//            if($p){
//                if($p->amount==3){
//                    return true;
//                }
//            }
//        }
//        return false;
//    }
}


