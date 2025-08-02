<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TenderSubmittedFile extends Model
{
    use HasFactory, SoftDeletes;

    public function requiredDocument(){
        return $this->belongsTo(RequiredDocument::class,'required_doc_id');
    }

    public function validateBy(){
        return $this->belongsTo(User::class,'file_checked_by');
    }
}
