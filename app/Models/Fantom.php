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
    protected $fillable = ['lead_id'];

    public function user(){
        return $this->belongsToThrough(User::class,  Lead::class);
    }
  
    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
   
}
