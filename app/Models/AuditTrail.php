<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditTrail extends Model
{
    use HasFactory;

    public function performedBy(){
        return $this->belongsTo(User::class,'perform_by','id');
    }
}
