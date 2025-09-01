<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class UniqueProductPlan extends Model
{
    use HasUuids;

    protected $guarded = [];

    public function product(){
        return $this->belongsTo(Product::class,'product_id','id');
    }

    public function network(){
        return $this->belongsTo(Network::class,'network_id','id');
    }

}
