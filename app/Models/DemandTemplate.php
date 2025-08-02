<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DemandTemplate extends Model
{
    use HasFactory;

    public function controlType(){
        return $this->belongsTo(ControlType::class);
    }

    public function demandType(){
        return $this->belongsTo(DemandType::class);
    }

    public function demandTemplatePVMS(){
        return $this->hasMany(DemandTemplatePvmsList::class);
    }

    public function createdBy(){
        return $this->belongsTo(User::class, 'created_by');
    }

    public function dmdUnit(){
        return $this->belongsTo(SubOrganization::class,'sub_org_id');
    }

    public function demandItemType()
    {
        return $this->belongsTo(ItemType::class,'demand_item_type_id','id');
    }
}
