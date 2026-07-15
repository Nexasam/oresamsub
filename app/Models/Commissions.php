<?php

namespace App\Models;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Commissions extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    public function beneficiary(){
        return $this->belongsTo(User::class,'beneficiary','id');
    }

    public function transaction(){
        return $this->belongsTo(Transaction::class,'transaction_id','id');
    }
}
