<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnnualDemandUnit extends Model
{
    use HasFactory;

    public function subOrganization(){
        return $this->belongsTo(SubOrganization::class,'sub_org_id');
    }

    public function annualDemand(){
        return $this->belongsTo(AnnualDemand::class,'annual_demand_id');
    }

    public function lastUnitApprovedRole(){
        return $this->belongsTo(UserApprovalRole::class,'last_approved_role');
    }

    public function unitDemandPvmsList(){
        return $this->hasMany(AnnualDemandPvmsUnitDemand::class);
    }
}
