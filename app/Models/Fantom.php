<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fantom extends Model
{
    use HasFactory;
/**
 * The attributes that are mass assignable.
 *
 * @var array
 */
    protected $fillable = ['bitrix_lead_id'];

    public function User()
    {
        Lead::where('bitrix_lead_id',$this->bitrix_lead_id)->first();
    }
    public function lead()
    {
        return $this->hasOne(Lead::class,"bitrix_lead_id","bitrix_lead_id");
    }
}
