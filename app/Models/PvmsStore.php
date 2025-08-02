<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PvmsStore extends Model
{
    use HasFactory;

    protected $table = 'pvms_store';

    public function batch()
    {
        return $this->belongsTo(BatchPvms::class, 'batch_pvms_id');
    }

    public function pvms()
    {
        return $this->belongsTo(PVMS::class);
    }

    public function onLoanItem()
    {
        return $this->belongsTo(OnLoanItem::class, 'on_loan_item_id');
    }

    public function workorderReceivePvms()
    {
        return $this->hasOne(WorkorderReceivePvms::class);
    }

    public function unit()
    {
        return $this->belongsTo(SubOrganization::class, 'sub_org_id');
    }

    public function ward()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}
