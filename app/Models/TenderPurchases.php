<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TenderPurchases extends Model
{
    protected $table = 'tender_purchase';

    use HasFactory,SoftDeletes;
    public function tender(){
        return $this->belongsTo(Tender::class,'tender_id');
    }
    public function vendor(){
        return $this->belongsTo(User::class,'created_by');
    }
}
