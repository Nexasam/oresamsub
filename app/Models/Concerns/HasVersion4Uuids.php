<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;

trait HasVersion4Uuids
{
    use HasUuids;

    public function newUniqueId(): string
    {
        return (string) Str::uuid();
    }
}
