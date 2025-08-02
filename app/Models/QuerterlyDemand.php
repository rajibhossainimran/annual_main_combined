<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuerterlyDemand extends Model
{
    use HasFactory;

    public function financialYear(){
        return $this->belongsTo(FinancialYear::class, 'financial_year');
    }

    public function querterlyDemandPvms(){
        return $this->hasMany(QuerterlyDemandPvms::class);
    }

    public function querterlyDemandReceive(){
        return $this->hasMany(QuerterlyDemandReceive::class);
    }

    public function querterlyDemandReceivePvms(){
        return $this->hasMany(QuerterlyDemandReceivePvms::class);
    }

    public function dmdUnit(){
        return $this->belongsTo(SubOrganization::class,'sub_org_id');
    }
}
