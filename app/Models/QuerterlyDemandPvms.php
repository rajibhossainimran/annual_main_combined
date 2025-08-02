<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuerterlyDemandPvms extends Model
{
    use HasFactory;

    public function pvms(){
        return $this->belongsTo(PVMS::class);
    }

    public function annualDemandPvmsUnitDemand(){
        return $this->belongsTo(AnnualDemandPvmsUnitDemand::class);
    }

    public function querterlyDemandReceivePvms(){
        return $this->hasMany(QuerterlyDemandReceivePvms::class);
    }

    public function batchPvms() {
        return $this->hasMany(BatchPvms::class,'pvms_id','pvms_id')->where('expire_date','>',Carbon::now());
    }
}
