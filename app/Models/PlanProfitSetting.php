<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PlanProfitSetting extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

     /**
     * each profit belongs to product 
    **/
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

      /**
     * each profit belongs to a network 
    **/
    public function network()
    {
        return $this->belongsTo(Network::class, 'network_id', 'id');
    }
}
