<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubOrganization extends Model
{
    use HasFactory;

    public function createdBy(){
        return $this->belongsTo(User::class,'created_by','id');
    }

    public function organizationFrom(){
        return $this->belongsTo(Organization::class,'org_id','id');
    }

    public function divisiomFrom(){
        return $this->belongsTo(Division::class,'division_id','id');
    }
    
    public function serviceFrom(){
        return $this->belongsTo(Service::class,'service_id','id');
    }
}
