<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuerterlyDemandReceive extends Model
{
    use HasFactory;

    public function querterlyDemand(){
        return $this->belongsTo(QuerterlyDemand::class);
    }

    public function querterlyDemandReceivePvms(){
        return $this->hasMany(QuerterlyDemandReceivePvms::class);
    }
}
