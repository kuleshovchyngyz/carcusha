<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notifier extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','bought','on_work'];
    public function user(){
        return $this->belongsTo(UserStatuses::class);
    }
}
