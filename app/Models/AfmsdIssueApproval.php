<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AfmsdIssueApproval extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_id',
        'purchase_type_id',
        'asign_qty',
        'transit_qty',
        'total_due',
        'afmsd_clerk',
        'afmsd_stockControlOfficer',
        'afmsd_groupIncharge',
        'delivery_by',
        'status',
    ];

    public function purchaseItem()
    {
        return $this->belongsTo(Purchase::class,'purchase_id');
    }

    public function purchaseType()
    {
        return $this->belongsTo(PurchaseType::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'delivery_by');
    }
    public function batchInfo()
        {
            return $this->hasMany(AfmsdIssueApprovalBatch::class);
        }
}
