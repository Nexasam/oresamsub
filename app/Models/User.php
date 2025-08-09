<?php

namespace App\Models;

// use App\Models\User;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;


class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, TwoFactorAuthenticatable, HasApiTokens, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $primaryKey='id';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function user_plan(){
        return $this->belongsTo(UserPlan::class,'user_plan_id','id');
    }

    public function role(){
        return $this->belongsTo(Role::class,'role_id','id');
    }

    public function upline(){
        return $this->belongsTo(User::class,'upline_id','id');
    }

    public function virtual_accounts(){
        return $this->hasMany(UserVirtualAccount::class,'user_id','id');
    }

    public function transactions(){
        return $this->hasMany(Transaction::class,'user_id','id');
    }

    public function latestTransaction()
    {
    return $this->hasOne(Transaction::class)->orderBy('created_at', 'desc');
    }



    // public function getRoleDetailsAttribute(){
    //     return $this->role()->first();
    // }

    
}
