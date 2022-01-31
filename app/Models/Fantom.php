<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fantom extends Model
{
    use HasFactory;
    use \Znck\Eloquent\Traits\BelongsToThrough;
/**
 * The attributes that are mass assignable.
 *
 * @var array
 */
    protected $fillable = ['bitrix_lead_id'];

    public function user(){
        return $this->belongsToThrough(User::class, Lead::class,null,'',[Lead::class=>'user_id',Fantom::class =>'bitrix_lead_id']);
    }
    public function lead()
    {
        return $this->belongsTo(Lead::class,'bitrix_lead_id','bitrix_lead_id');
    }

}
