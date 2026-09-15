<?php

namespace App\Models;

use App\Models\AdminWebhookString;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FundingOption extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    protected $hidden = ['virtual_account_id_number'];

    protected $casts = [
        'virtual_account_id_number' => 'encrypted',
    ];
    public function bank_codes(){
        return $this->hasMany(FundingOptionBankCodes::class,'funding_option_id','id');
    }

    public function webhook_string(){
        return $this->hasOne(AdminWebhookString::class,'funding_option_id','id');
    }

   

}
