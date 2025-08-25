<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletLog extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    public function actionBy(){
        return $this->belongsTo(User::class,'action_by','id');
    }

    public function user(){
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function transaction(){
        return $this->belongsTo(User::class,'transaction_id','id');
    }

}
