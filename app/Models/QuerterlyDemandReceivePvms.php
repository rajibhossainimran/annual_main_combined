<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuerterlyDemandReceivePvms extends Model
{
    use HasFactory;

    public function batchPvms(){
        return $this->belongsTo(BatchPvms::class);
    }

    public function querterlyDemandReceive(){
        return $this->belongsTo(QuerterlyDemandReceive::class);
    }
    
    public function querterlyDemandPvms(){
        return $this->belongsTo(QuerterlyDemandPvms::class);
    }
}
