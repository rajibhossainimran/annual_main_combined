<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DemandRepairPvms extends Model
{
    use HasFactory;

    public function PVMS(){
        return $this->belongsTo(PVMS::class);
    }
    public function rateRunningPVMSDemand(){
        return $this->hasMany(RateRunningPvms::class,'pvms_id','p_v_m_s_id')->where('rate_running_pvms.end_date','>',Carbon::now())->where('rate_running_pvms.start_date','<=',Carbon::now());
    }

    public function notesheet(){
        return $this->hasOne(NotesheetDemandPVMS::class,'demand_repair_pvms_id','id');
    }

}
