<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    /**
     * each product belongs to product category 
    **/
    public function product_category()
    {
        return $this->belongsTo(ProductCategory::class, 'product_categories_id', 'id')->where('active_status',1);
    }

}
