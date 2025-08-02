<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Specification extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'status', 'created_by'];

    protected $table = 'specifications';

    public function user() {
        return $this->belongsTo('App\Models\User');
    }
}
