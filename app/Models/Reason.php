<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reason extends Model
{
    use HasFactory;
    protected $fillable = ['table_id','reason_name','user_id_who_referred'];
    public function payment(){
        return $this->belongsTo(Payment::class, 'id','reason');
    }
    public function lead(){
        return Lead::where('bitrix_user_id',$this->attributes['table_id'])->first();
    }
}
