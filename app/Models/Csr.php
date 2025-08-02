<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Csr extends Model
{
    use HasFactory;

    protected $table = 'csr';

    public function PVMS()
    {
        return $this->belongsTo(PVMS::class, 'pvms_id');
    }
    public function tender()
    {
        return $this->belongsTo(Tender::class, 'tender_id');
    }

    public function csrPvmsApproval()
    {
        return $this->hasMany(CsrApproval::class, 'csr_id');
    }

    public function csrDemands()
    {
        return $this->hasMany(CsrDemand::class, 'csr_id');
    }

    public function vandorPerticipate()
    {
        return $this->hasMany(VendorBidding::class, 'csr_id')->orderBy('offered_unit_price', 'asc');
    }
    public function vandorPerticipateWithValidDoc()
    {
        return $this->hasMany(VendorBidding::class, 'csr_id')->where('is_uploaded_file_checked', 1)->where('is_valid', 1);
    }
    public function selectedBidder()
    {
        return $this->belongsTo(VendorBidding::class, 'approved_vendor');
    }
    public function hod()
    {
        return $this->belongsTo(User::class, 'hod_user');
    }
}
