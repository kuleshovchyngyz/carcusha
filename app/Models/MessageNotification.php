<?php
namespace App\Models;
use App\Models\Lead;
use App\Models\User;
//use App\Traits\MessageNotificationTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageNotification extends Model
{
    use HasFactory;
    use MessageNotificationTrait;
    protected $fillable = ['user_id','seen','message','lead_id'];
    public function lead(){
        return $this->belongsTo(Lead::class);
    }
    public function user(){
        return $this->belongsTo(User::class);
    }
}
