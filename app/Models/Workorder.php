<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workorder extends Model
{
    use HasFactory;

    public function vendor(){
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function workorderPvms(){
        return $this->hasMany(WorkorderPvms::class);
    }

    public function documents(){
        return $this->hasMany(WorkorderDocument::class);
    }

    public function financialYear(){
        return $this->belongsTo(FinancialYear::class);
    }

    public function workorderReceive(){
        return $this->hasMany(WorkorderReceive::class);
    }

}
