<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Automation extends Model
{
    use HasFactory, HasUuids;
    
    protected $guarded = ['id'];

    protected $hidden = [
        // 'created_at',
        // 'updated_at',
        'api_secret_key',
        'api_public_key',
        'api_password',
    ];

     

}
