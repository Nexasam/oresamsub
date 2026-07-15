<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AdminColorSetting extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];
}
