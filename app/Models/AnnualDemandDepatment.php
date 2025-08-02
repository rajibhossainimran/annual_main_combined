<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnnualDemandDepatment extends Model
{
    use HasFactory;

    public function department(){
        return $this->belongsTo(ItemDepartment::class, 'department_id');
    }

    public function pvmsList(){
        return $this->hasMany(AnnualDemandPvms::class);
    }
}
