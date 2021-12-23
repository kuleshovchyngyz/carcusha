<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    use \Staudenmeir\EloquentHasManyDeep\HasRelationships;
    protected $table = 'notifications';
    protected $fillable = [
        'lead_id',
        'f_lead_id',
        'status',
        'application_token',
        'event'

    ];
    public function userStatus(){
        return $this->hasOneDeep(UserStatuses::class, [Status::class],['index'],['status']);
    }
    public function user_status(){

        return $this->leadStatus->user_statuses;
    }
    public function status(){
        return Status::where('index',$this->attributes['status'])->first();
    }
    public function leadStatus(){
        return $this->hasOne(Status::class,'index','status');
    }
}
