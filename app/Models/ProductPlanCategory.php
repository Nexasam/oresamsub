<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductPlanCategory extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    public function product(){
        return $this->belongsTo(Product::class,'product_id','id');
    }

    public function network(){
        return $this->belongsTo(Network::class,'network_id','id');
    }

    public function automation(){
        return $this->belongsTo(Automation::class,'automation_id','id');
    }

    
    

}
