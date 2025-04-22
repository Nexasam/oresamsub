<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MaxCrystalPaymentsPendingApproval extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    public function user(){
        return $this->belongsTo(User::class,'user_id','id');
    }

}
