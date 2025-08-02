<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CsrDemand extends Model
{
    use HasFactory;

    public function notesheet(){
        return $this->belongsTo(Notesheet::class,'notesheet_id');
    }
}
