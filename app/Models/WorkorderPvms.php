<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkorderPvms extends Model
{
    use HasFactory;

    public function pvms(){
        return $this->belongsTo(PVMS::class, 'pvms_id', 'id');
    }

    public function workorderReceivePvms(){
        return $this->hasMany(WorkorderReceivePvms::class);
    }

    public function workorder(){
        return $this->belongsTo(Workorder::class, 'workorder_id');
    }
}
