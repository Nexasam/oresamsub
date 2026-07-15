<?php

namespace App\Models;

use App\Models\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FundingWebhookPayload extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    public function user(){
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function funding_promo(){
        return $this->belongsTo(WalletFundingPromo::class,'wallet_funding_promo_id','id');
    }


}
