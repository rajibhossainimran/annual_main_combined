<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DemandApproval extends Model
{
    use HasFactory;

    public function user_approval_key()
    {
        return $this->hasOne(UserApprovalRole::class, 'role_key', 'role_name');
    }
}
