<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DemandPvms extends Model
{
    use HasFactory;

    public function PVMS()
    {
        return $this->belongsTo(PVMS::class);
    }

    public function notesheet()
    {
        return $this->hasOne(NotesheetDemandPVMS::class, 'demand_pvms_id', 'id');
    }

    public function rateRunningPVMSDemand()
    {
        return $this->hasMany(RateRunningPvms::class, 'pvms_id', 'p_v_m_s_id')->where('rate_running_pvms.end_date', '>', Carbon::now())->where('rate_running_pvms.start_date', '<=', Carbon::now());
    }

    public function centralStock()
    {
        return $this->hasOne(CentralStock::class, 'pvms_id', 'p_v_m_s_id');
    }

    public function orgStock()
    {
        return $this->hasOne(SubOrgStock::class, 'pvms_id', 'p_v_m_s_id');
    }

    public function purchaseOrder()
    {
        return $this->hasOne(PurchaseType::class, 'demand_pvms_id')->where('purchase_types.status', 'approved');
    }

    public function purchaseOrderRequest()
    {
        return $this->hasOne(PurchaseType::class, 'demand_pvms_id')->whereIn('purchase_types.status', ['approved', 'pending']);
    }

    // DemandPvms.php
    public function itemType()
    {
        return $this->belongsTo(ItemType::class, 'item_types_id', 'id');
    }
}
