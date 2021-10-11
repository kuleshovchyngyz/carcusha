<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentAmount extends Model
{
    use HasFactory;
    protected $fillable = ['reason_of_payment','amount'];
    public function type($str){
        $p = PaymentAmount::where('reason_of_payment',$str)->first();
        return $p->amount;
    }
}
