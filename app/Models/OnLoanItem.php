<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnLoanItem extends Model
{
    use HasFactory;

    public function itemReceieve(){
        return $this->hasMany(OnLoanItemReceive::class,'on_loan_item_id');
    }

    public function PVMS(){
        return $this->belongsTo(PVMS::class,'pvms_id');
    }

    public function onLoan(){
        return $this->belongsTo(OnLoan::class,'on_loan_id');
    }

    public function pvmsStore(){
        return $this->hasMany(PvmsStore::class,'on_loan_item_id');
    }
}
