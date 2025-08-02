<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CsrCoverLetter extends Model
{
    use HasFactory;

    public function presidents(){
        return $this->hasMany(CsrCoverLetterPresident::class,'csr_cover_letter_id');
    }

    public function members(){
        return $this->hasMany(CsrCoverLetterMember::class,'csr_cover_letter_id');
    }

    public function coOperativeMembers(){
        return $this->hasMany(CsrCoverLetterCoOperativeMember::class,'csr_cover_letter_id');
    }
}
