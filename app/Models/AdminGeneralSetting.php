<?php

namespace App\Models;

use App\Models\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminGeneralSetting extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    
}
