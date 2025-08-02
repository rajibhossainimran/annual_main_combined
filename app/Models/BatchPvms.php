<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BatchPvms extends Model
{
    use HasFactory;

    public function unitStock()
    {
        return $this->hasMany(PvmsStore::class)->where('sub_org_id', auth()->user()->sub_org_id ? auth()->user()->sub_org_id : 2);
    }

    public function workorderPvms()
    {
        return $this->belongsTo(WorkorderPvms::class);
    }
}
