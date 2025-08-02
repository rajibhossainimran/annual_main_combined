<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CsrApproval extends Model
{
    use HasFactory;

    public function bidder(){
        return $this->belongsTo(VendorBidding::class,'selected_biddder_id');
    }
    public function approved_by(){
        return $this->belongsTo(User::class,'approved_by');
    }
}
