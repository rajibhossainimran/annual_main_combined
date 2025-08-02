<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseType extends Model
{
    use HasFactory;

    protected $table = 'purchase_types';

    public function pvms()
    {
        return $this->belongsTo(PVMS::class, 'pvms_id');
    }
    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }
    public function demand()
    {
        return $this->belongsTo(Demand::class);
    }
    public function purchaseDelivery()
    {
        return $this->hasMany(PurchaseTypeDelivery::class);
    }
    public function batchPvms()
    {
        return $this->hasMany(BatchPvms::class, 'pvms_id', 'pvms_id')->where('expire_date', '>', Carbon::now());
    }
}
