<?php

namespace App\Models;

use App\Models\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'access_permissions';

    protected $guarded = [];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'permission_role');
    }
}
