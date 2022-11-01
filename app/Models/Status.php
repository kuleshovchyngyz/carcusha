<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;
    protected $table = 'statuses';

    protected $fillable = [
        'name',
        'index',
        'color',
        'ID_on_bitrix',
        'status_type',
        'update'

    ];



    public function leads(){
        return $this->hasMany(Lead::class,'status_id');
    }
    public function type(){
        //return $this->attributes['index'];
        $pending = [20,2,30,31,9,23,32,33,36,'ASSIGNED','CANNOT_CONTACT'];
        $rejected = [1,26,13,4,10,29,27,24,34,25,35,37,'JUNK'];
        if(in_array($this->attributes['index'],$pending)){
            return 'initial';
        }
        if(in_array($this->attributes['index'],$rejected)){
            return 'rejected';
        }
        if($this->attributes['index']=='CONVERTED'){
            return 'success';
        }
        if($this->attributes['index']=='NEW'){
            return 'NEW';
        }
    }
    public function user_statuses(){
        return $this->hasOne(UserStatuses::class);
    }

}
