<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenderNotesheet extends Model
{
    use HasFactory;

    public function notesheet()
    {
        return $this->hasOne(Notesheet::class, 'id', 'notesheet_id');
    }
}
