<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FundingOptionBankCodes extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    public function virtual_user_account(){
        return $this->belongsTo(UserVirtualAccount::class,'id','user_id');
    }

   

}
