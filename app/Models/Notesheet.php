<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notesheet extends Model
{
    use HasFactory;

    public function notesheetDemandPVMS(){
        return $this->hasMany(NotesheetDemandPVMS::class,'notesheet_id');
    }

    public function notesheetType(){
        return $this->belongsTo(ItemType::class,'notesheet_item_type');
    }

    public function approval(){
        return $this->hasMany(NotesheetApproval::class,'notesheet_id');
    }

    public function tenderNotesheet(){
        return $this->hasOne(TenderNotesheet::class,'notesheet_id','id');
    }
}
