<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    protected $table = 'notifications';
    protected $fillable = [
        'lead_id',
        'f_lead_id',
        'status',
        'application_token',
        'event'

    ];
    public function user_status(){
        return Status::where('index',$this->attributes['status'])->first()->user_statuses;
    }
    public function status(){
        return Status::where('index',$this->attributes['status'])->first();
    }
}
