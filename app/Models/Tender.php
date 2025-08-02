<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tender extends Model
{
    use HasFactory;

    public function tenderNotesheet()
    {
        return $this->hasMany(TenderNotesheet::class, 'tender_id');
    }

    public function tenderCsr()
    {
        return $this->hasMany(Csr::class, 'tender_id');
    }

    public function csrs()
    {
        return $this->hasMany(Csr::class, 'tender_id');
    }

    public function vendorSubmittedFiles()
    {
        return $this->hasMany(TenderSubmittedFile::class, 'tender_id');
    }

    public function tenderPayments()
    {
        return $this->hasMany(TenderPurchases::class, 'tender_id');
    }

    public function requiredFiles()
    {
        return $this->hasMany(TenderRequiredDocument::class, 'tender_id');
    }

    public function coverLetter()
    {
        return $this->belongsTo(CsrCoverLetter::class, 'id', 'tender_id');
    }
}
