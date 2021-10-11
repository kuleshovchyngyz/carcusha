<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendingAmount extends Model
{
    use HasFactory;
    protected $table = 'pending_amount';
    protected $fillable = ['status','payment_id'];
}
