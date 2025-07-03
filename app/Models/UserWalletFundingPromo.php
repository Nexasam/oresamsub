<?php

namespace App\Models;

use App\Models\FundingOption;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserWalletFundingPromo extends Model
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
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
