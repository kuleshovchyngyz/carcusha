<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;
    protected $fillable =[ 'user_id','city','email','number','card_number','card_security_code','card_date','email_notification','number_notification','telegram_id' ];
}
