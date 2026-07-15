<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasVersion4Uuids as HasUuids;

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

    public function product_plans(){
        return $this->hasMany(ProductPlan::class,'unique_product_plan_id','id');
    }

}
