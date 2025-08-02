<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnnualDemandPvms extends Model
{
    use HasFactory;
    protected $table = 'annual_demand_pvms';

    public function PVMS(){
        return $this->belongsTo(PVMS::class,'pvms_id');
    }

    public function annualDemandDepartment(){
        return $this->belongsTo(AnnualDemandDepatment::class,'annual_demand_depatment_id');
    }
}
