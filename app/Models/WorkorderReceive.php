<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkorderReceive extends Model
{
    use HasFactory;

    public function workorder(){
        return $this->belongsTo(Workorder::class);
    }

    public function pvmsStore(){
        return $this->hasMany(PvmsStore::class);
    }

    public function documents(){
        return $this->hasMany(WorkorderReceiveDocument::class);
    }
}
