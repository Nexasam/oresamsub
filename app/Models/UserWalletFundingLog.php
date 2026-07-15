<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\HasVersion4Uuids as HasUuids;

class UserWalletFundingLog extends Model
{
    use HasFactory, HasUuids;

    // Allow mass assignment for all fields
    protected $guarded = [];

    /**
     * The user that owns this funding log
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * The funding option associated with this log
     */
    public function fundingOption()
    {
        return $this->belongsTo(FundingOption::class, 'funding_option_id', 'id');
    }

    /**
     * The promo associated with this log, if any
     */
    public function promo()
    {
        return $this->belongsTo(UserWalletFundingPromo::class, 'promo_id', 'id');
    }
}
