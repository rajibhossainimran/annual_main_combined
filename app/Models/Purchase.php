<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;
    protected $table = 'purchase';

    public function purchasePvms()
    {
        return $this->hasMany(PurchaseType::class);
    }

    public function purchasePvmsDeliveryComplete()
    {
        return $this->hasMany(PurchaseType::class)->whereColumn('request_qty', 'received_qty');
    }

    public function dmdUnit()
    {
        return $this->belongsTo(SubOrganization::class, 'sub_org_id');
    }

    public function purchaseTypes()
    {
        return $this->hasMany(PurchaseType::class, 'purchase_id');
    }

    public function afmsdIssueApproval()
    {
        return $this->hasMany(AfmsdIssueApproval::class, 'purchase_id');
    }

    public function subOrganization()
    {
        return $this->belongsTo(SubOrganization::class, 'sub_org_id', 'id');
    }

    public function sendTo()
    {
        return $this->belongsTo(User::class, 'send_to', 'id');
    }

    public function financialYear()
{
    return $this->belongsTo(FinancialYear::class, 'financial_year_id', 'id');
}

}
