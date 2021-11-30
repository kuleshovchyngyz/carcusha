<?php

namespace App\support\Leads;

use App\Models\Ad;
use App\Models\Lead;
use App\Models\Status;
use App\Models\UserStatuses;
use App\support\Bitrix\ApiConnect;

class UpdatingLeadStatus
{

    protected $new_status, $old_status, $user, $lead,  $statuses;
    public function __construct($id,$status){
        $this->lead = Lead::where('bitrix_user_id',$id)->first();
        $this->user = $this->lead->user;
        $this->checkForAds();


        $this->new_status = Status::find($status);
        $notify = new Notify($this->lead,$this->new_status);

        if($notify->notify()){
            new NewPayment($this->lead, $this->new_status);
//            new Pay($this->lead, $this->new_status);
        }
    }



    public function checkForAds(){
        $str = Ad::first();
        $str = explode("\r\n", $str->name);
        $str = collect($str);
        $dealData = new ApiConnect('crm.lead.get', ['id' =>  $this->lead->bitrix_user_id]);
        if($dealData->getResponse()===false){
            $this->lead->checked = 1;
            $this->lead->save();
            return true;
        }
        $arr = [];
        $field = trim($dealData->getFieldName('isInAvito.ru')) ? trim($dealData->getFieldName('isInAvito.ru')) : false;
        if( $field !== false ) {
            $arr[] = $field;
        }
        $field = trim($dealData->getFieldName('isInAuto.ru')) ? trim($dealData->getFieldName('isInAuto.ru')) : false;
        if( $field !== false ) {
            $arr[] = $field;
        }
        $arr = collect($arr);
        $diff = $arr->diff($str);
        if( $diff->count() > 0 )
        {
            $this->lead->checked_texts = implode(',' , $diff->toArray());
            $this->lead->checked = 0;
            $this->lead->save();
            return true;
        }
        $this->lead->checked_texts = null;
        $this->lead->checked = 1;
        $this->lead->save();
        return false;
    }


    public function defroze_status_ids(){
        $us = UserStatuses::where('name','like','%разморожены%')->orwhere('name','like','%разблокированы%');
        $us = $us->where('name','not like','%Согласен продать%')->pluck('status_id');
        if($this->check_for_lead_payments_initial()){
            $this->defroze_initial_lead_payment();
        }
        return $us->contains($this->new_status->id);
    }


}
