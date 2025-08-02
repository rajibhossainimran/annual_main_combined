<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotesheetDemandPVMS extends Model
{
    use HasFactory;
    protected $table = 'notesheet_demand_pvms';

    public function demandPVMS(){
        return $this->belongsTo(DemandPvms::class,'demand_pvms_id');
    }

    public function demandRepairPVMS(){
        return $this->belongsTo(DemandRepairPvms::class,'demand_repair_pvms_id');
    }

    public function demand(){
        return $this->belongsTo(Demand::class,'demand_id');
    }

    public function PVMS(){
        return $this->belongsTo(PVMS::class,'pvms_id');
    }
}
