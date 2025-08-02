<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseTypeDelivery extends Model
{
    use HasFactory;
    protected $table = 'purchase_type_deliveries';

    public function store(){
        return $this->belongsTo(PvmsStore::class,'pvms_store_id');
    }
}
