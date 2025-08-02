<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    public function createdBy(){
        return $this->belongsTo(User::class,'created_by','id');
    }

    public function subOrganizationFrom(){
        return $this->belongsTo(SubOrganization::class,'sub_org_id','id');
    }
}
