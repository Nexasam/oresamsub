<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UsedWalletFundingPromo extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];
}
