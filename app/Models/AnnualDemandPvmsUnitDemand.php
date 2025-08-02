<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnnualDemandPvmsUnitDemand extends Model
{
    use HasFactory;

    public function annualDemandPvms(){
        return $this->belongsTo(AnnualDemandPvms::class,'annual_demand_pvms_id');
    }

    public function annualDemandUnit(){
        return $this->belongsTo(AnnualDemandUnit::class,'annual_demand_unit_id');
    }

    public function querterlyDemandPvms(){
        return $this->hasOne(QuerterlyDemandPvms::class);
    }
}
