<?php

namespace App\Traits\Utils;


trait Utils{

    public function statusText($status)
    {
        return match ($status) {
            1 => 'Success',
            -1 => 'Failed',
            0 => 'Pending',
            2 => 'Refunded',
            3 => 'Processing',
            default => 'Unknown',
        };
    }

}