<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FundingOption extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];
    public function bank_codes(){
        return $this->hasMany(FundingOptionBankCodes::class,'funding_option_id','id');
    }

}
