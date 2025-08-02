<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TenderRequiredDocument extends Model
{
    use HasFactory;

    public function requiredDocument(){
        return $this->belongsTo(RequiredDocument::class,'required_document_id');
    }
}
