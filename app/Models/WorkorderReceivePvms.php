<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkorderReceivePvms extends Model
{
    use HasFactory;

    public function pvmsStore(){
        return $this->belongsTo(PvmsStore::class);
    }

    public function onLoanItem(){
        return $this->belongsTo(OnLoanItem::class,'on_loan_item_id');
    }

    public function workorderPvms(){
        return $this->belongsTo(WorkorderPvms::class,'workorder_pvms_id');
    }

    public function workorderReceive(){
        return $this->belongsTo(WorkorderReceive::class);
    }
}
