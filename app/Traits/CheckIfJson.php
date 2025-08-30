<?php

namespace App\Traits;

trait CheckIfJson{
  
public function isObjectOrArrayJson($value): bool
{
    if (!is_string($value)) {
        return false;
    }

    $decoded = json_decode($value);

    return (json_last_error() === JSON_ERROR_NONE) && is_object($decoded) || is_array($decoded);
}

}