<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Demand extends Model
{
    use HasFactory;

    protected $appends = ['approval_date'];

    public function controlType()
    {
        return $this->belongsTo(ControlType::class);
    }

    public function financialYear()
    {
        return $this->belongsTo(FinancialYear::class, 'financialYear');
    }

    public function demandPVMS()
    {
        return $this->hasMany(DemandPvms::class);
    }
    public function demandDocuments()
    {
        return $this->hasMany(DemandDocument::class);
    }

    public function demandRepairPVMS()
    {
        return $this->hasMany(DemandRepairPvms::class);
    }

    public function demandPVMSOnlyNotesheet()
    {
        return $this->hasMany(DemandPvms::class)->where('purchase_type', 'notesheet')->doesntHave('rateRunningPVMSDemand');
    }

    public function demandPVMSRateRunningOnlyNotesheet()
    {
        return $this->hasMany(DemandPvms::class)->where('purchase_type', 'notesheet')->whereHas('rateRunningPVMSDemand');
    }

    public function demandRepairPVMSOnlyNotesheet()
    {
        return $this->hasMany(DemandRepairPvms::class)->where('purchase_type', 'notesheet')->doesntHave('rateRunningPVMSDemand');
    }
    public function demandRepairPVMSRateRunningOnlyNotesheet()
    {
        return $this->hasMany(DemandRepairPvms::class)->where('purchase_type', 'notesheet')->whereHas('rateRunningPVMSDemand');
    }

    public function demandType()
    {
        return $this->belongsTo(DemandType::class);
    }

    public function dmdUnit()
    {
        return $this->belongsTo(SubOrganization::class, 'sub_org_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approval()
    {
        return $this->hasMany(DemandApproval::class);
    }
    public function last_approval()
    {
        return $this->hasOne(DemandApproval::class)->latest();
    }

    public function demandItemType()
    {
        return $this->belongsTo(ItemType::class, 'demand_item_type_id', 'id');
    }

    public function subOrganization()
    {
        return $this->belongsTo(SubOrganization::class, 'sub_org_id');
    }

    public function getApprovalDateAttribute()
    {
        return DemandApproval::where('demand_id', $this->id)
            ->where('step_number', 0)
            ->first();
    }
}
