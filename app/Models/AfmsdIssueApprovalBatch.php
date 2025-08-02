<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AfmsdIssueApprovalBatch extends Model
{
    use HasFactory;
    protected $table = 'afmsd_issue_approval_batchs';
     protected $fillable = [
        'afmsd_issue_approval_id',
        'batchPvms_id',
        'qty',
   
    ];
}
