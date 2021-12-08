<?php

namespace App\support\Leads;
use App\Models\MessageNotification;
use App\Models\Notification;
use App\Models\Status;
use Carbon\Carbon;


class Notify
{
    public $lead, $new_status, $message, $user, $history_of_lead, $old_status, $statuses, $rejected_statuses,$typeOfStatus;

    public function __construct($lead, $new_status){
        $this->lead = $lead;
        $this->user = $this->lead->user;
        $this->new_status = $new_status;
        $this->typeOfStatus = $this->new_status->user_statuses->amount;
        $this->statuses = Status::all();
        $this->set_rejected_statuses();

    }
    public function notify(){
        $sent = $this->create_notification();
        if($sent){
            $this->lead->status_id = $this->new_status->id;
            $this->lead->save();
            $this->statuses = Status::all();
            $this->history_of_lead = Notification::where('f_lead_id',$this->lead->id)
                ->orderby('updated_at','DESC')
                ->get();
            if ($this->history_of_lead->count()>1){
                $this->old_status = $this->statuses->where('index',$this->history_of_lead[1]->status)->first();
            }
            $this->set_message();
            $this->send_notification();
        }
        return $sent;
    }
    public function send_notification(){
        $notify = true;
        if( $this->user->notifier!==null ){
            $notify = false;
            if( $this->new_status->id == 14 && $this->user->notifier->bought ){
                $notify = true;
            }else if($this->new_status->id != 14 && $this->user->notifier->on_work ){
                $notify = true;
            }
        }
        if($this->new_status->user_statuses->notify == 1 && $notify ){
            $date = Carbon::now()->format('d.m.Y');
            $time = Carbon::now()->format('H:i:s');

            if ($this->history_of_lead->count()>1){
                $date = date('d.m.Y', strtotime($this->history_of_lead->first()->updated_at));
                $time = date('H:i:s', strtotime($this->history_of_lead->first()->updated_at));
            }
           // $str = $date.' в '.$time.'. Машина #'.$this->lead->bitrix_lead_id.': ';
            $str = 'Авто #'.$this->lead->bitrix_lead_id.': ';
            $this->message = $str.$this->message;
            $this->create_message_notification();
        }

    }
    public function create_message_notification(){
        MessageNotification::create([
            'user_id'=>$this->user->id,
            'seen'=>false,
            'message'=>$this->message,
            'lead_id'=>$this->lead->bitrix_lead_id,
        ]);
    }
    public function set_message(){
        if($this->new_status->user_statuses->notify==1){
//            $this->message =  $this->new_status->user_statuses->name;
            $this->message =  $this->new_status->user_statuses->name.' '.shortCodeParse($this->new_status->user_statuses->comments);
            if( $this->user->user_who_referred()!==false && $this->user->payments->where('status_group','successCONVERTED')->count()===0 && $this->typeOfStatus=='success'){
                $this->message =  $this->new_status->user_statuses->name.' '.shortCodeParse($this->new_status->user_statuses->comments,[],[],true);
            }
        }
    }
    public function create_notification(): bool
    {
        $n = Notification::where('lead_id',$this->lead->bitrix_lead_id)
            ->orderby('updated_at','DESC')
            ->pluck('status')
            ->unique();
        $s = collect([]);
        if ($n->count()>0){
            $s = Status::whereIn('index',$n)->pluck('id');
        }

        //dump($s);
        //dump($this->rejected_statuses);
        if( $s->contains(14) ){
            return false;
        }
        if( in_array($this->new_status->id, $this->rejected_statuses) ){
            if($this->checkForRejectedStatuses($s)){
                $this->NotificationCreate();
                return true;
            }
            return false;
        }else if(!$s->contains($this->new_status->id)  ){
            $this->NotificationCreate();
            return true;
        }
        return false;
    }
    public function NotificationCreate(){
        Notification::create([
            'lead_id' => $this->lead->bitrix_lead_id,
            'f_lead_id' => $this->lead->id,
            'event' => $this->new_status->color,
            'status' => $this->new_status->index
        ]);
    }
    public function checkForRejectedStatuses( $collection): bool
    {
        foreach ($this->rejected_statuses as $r){
            if($collection->contains($r)){
                return false;
            }
        }
        return true;
    }

    public function set_rejected_statuses(){
        foreach ($this->statuses as $key=>$status){
            if($key<26){
                if( $status->user_statuses->notify == 1 && $status->user_statuses->amount() < 0  ) {
                    $this->rejected_statuses[] = $status->id;
                }

            }
        }
    }
}
