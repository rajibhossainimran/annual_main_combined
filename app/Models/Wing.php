<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wing extends Model
{
    use HasFactory;

    public function subOrganization(){
        return $this->belongsTo(SubOrganization::class,'sub_organization_id');
    }
}
