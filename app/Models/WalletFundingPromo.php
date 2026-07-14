<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WalletFundingPromo extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    
    /**
    * each funding belongs to a user 
    **/
    public function funding_option()
    {
        return $this->belongsTo(FundingOption::class, 'funding_option_id', 'id');
    }

    /**
    * each funding belongs to a user 
    **/
    public function beneficiary()
    {
        return $this->belongsTo(User::class, 'beneficiary', 'id');
    }
}
