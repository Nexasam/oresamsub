<?php

namespace App\Models;

use App\Models\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserVirtualAccount extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

       /**
     * each card belongs to a user 
    **/
    public function funding_option()
    {
        return $this->belongsTo(FundingOption::class, 'funding_option_id', 'id');
    }

    
}
