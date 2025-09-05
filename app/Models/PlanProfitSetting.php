<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PlanProfitSetting extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = ['id'];
}
