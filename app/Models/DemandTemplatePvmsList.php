<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DemandTemplatePvmsList extends Model
{
    use HasFactory;

    public function PVMS(){
        return $this->belongsTo(PVMS::class);
    }
}
