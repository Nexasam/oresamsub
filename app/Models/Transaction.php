<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    protected $casts = [
        // 'created_at' => 'datetime:Africa/Lagos',
        'retry_count' => 'integer',
        'balance_before' => 'float',
        'balance_after' => 'float',
    ];
    

    public function user(){
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function manual_processing_locker(){
        return $this->belongsTo(User::class,'locked_for_manual_processing','id');
    }

    public function product_plan(){
        return $this->belongsTo(ProductPlan::class,'product_plan_id','id');
    }

    public function getCreatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->timezone('Africa/Lagos')->toIso8601String();
    }

    public function getUpdatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->timezone('Africa/Lagos')->toIso8601String();
    }



}
