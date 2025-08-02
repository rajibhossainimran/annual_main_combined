<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnnualDemand extends Model
{
    use HasFactory;

    public function financialYear(){
        return $this->belongsTo(FinancialYear::class);
    }

    public function departmentList(){
        return $this->hasMany(AnnualDemandDepatment::class, 'annual_demand_id');
    }
    public function unitDemandList(){
        return $this->hasMany(AnnualDemandUnit::class, 'annual_demand_id');
    }

    public function lastListApprovedRole(){
        return $this->belongsTo(UserApprovalRole::class,'last_list_approved_role');
    }
}
