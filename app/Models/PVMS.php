<?php

namespace App\Models;

use App\Models\AccountUnit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Specification;
use App\Models\ItemSections;
use App\Models\ItemDepartment;
use App\Models\ItemGroup;
use App\Models\ItemType;
use App\Models\ControlType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

class PVMS extends Model
{
    protected $table = 'p_v_m_s';

    use HasFactory, SoftDeletes;

    protected $fillable = ['pvms_id', 'pvms_name', 'nomenclature', 'account_units_id', 'account_units_id', 'item_types_id'];

    public function unitName()
    {
        return $this->belongsTo(AccountUnit::class, 'account_units_id', 'id');
    }

    public function specificationName()
    {
        return $this->belongsTo(Specification::class, 'specifications_id', 'id');
    }

    public function itemSectionName()
    {
        return $this->belongsTo(ItemSections::class, 'item_sections_id', 'id');
    }

    public function itemDepartmantName()
    {
        return $this->belongsTo(ItemDepartment::class, 'item_departments_id', 'id');
    }

    public function itemGroupName()
    {
        return $this->belongsTo(ItemGroup::class, 'item_groups_id', 'id');
    }

    public function itemTypename()
    {
        return $this->belongsTo(ItemType::class, 'item_types_id', 'id');
    }

    public function rateRunningContract()
    {
        return $this->hasMany(RateRunningPvms::class, 'pvms_id')->where('rate_running_pvms.end_date', '>', Carbon::now())->where('rate_running_pvms.start_date', '<=', Carbon::now());
    }

    public function controlTypeName()
    {
        return $this->belongsTo(ControlType::class, 'control_types_id', 'id');
    }

    public function authorizedEquipment()
    {
        return $this->hasOne(AuthorizedEquipment::class, 'pvms_id');
    }

    public function batchList()
    {
        return $this->hasMany(BatchPvms::class, 'pvms_id', 'id')->where('expire_date', '>', Carbon::now());
    }

    public function onLoanItemsNotReceived()
    {
        return $this->hasMany(OnLoanItem::class, 'pvms_id')->where('qty', '>', 'receieved_qty');
    }

    public function accountUnit()
    {
        return $this->belongsTo(AccountUnit::class, 'account_units_id');
    }
}
