<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnLoan extends Model
{
    use HasFactory;

    public function onLoanItemList(){
        return $this->hasMany(OnLoanItem::class,'on_loan_id');
    }

    public function vendor(){
        return $this->belongsTo(User::class,'vendor_id');
    }
}
