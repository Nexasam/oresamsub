<?php

namespace App\Models;

use App\Models\FundingOption;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserWalletFundingPromo extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    protected $casts = [
        'funding_option_ids' => 'array',
    ];    


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
